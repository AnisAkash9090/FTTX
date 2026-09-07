<?php

namespace App\Http\Controllers;

use App\Models\OltConfig;
use App\Models\olt_oid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;
use App\Models\QueryRoute;
class OltConnectionController extends Controller
{
    public function olt()
    {
        // 1. Get all active OLT configurations
        $oltConfigs = OltConfig::where('sts', 'active')->get();

        // 2. Fetch OIDs matched by assign_for foreign key grouped by OLT ID
        $oltOids = olt_oid::whereIn('assign_for', $oltConfigs->pluck('id'))->get()->groupBy('assign_for');

        return view('olt_check', compact('oltConfigs', 'oltOids'));
    }

public function runSnmp(Request $request)
{
    $oltId = $request->input('olt_id');
    // Fetch the OLT configuration
    $olt = OltConfig::find($oltId);
    
    if (!$olt) {
        return response()->json(['status' => 'error', 'message' => 'OLT not found'], 404);
    }
  // 2) Determine the script based on brand and type
    $scriptName = 'snmp.py'; // Default script

    $routeConfig = QueryRoute::where('brand', $olt->olt_brand)
        ->where('type', $olt->type)
        ->first();

    if ($routeConfig && !empty($routeConfig->route)) {
        $scriptName = $routeConfig->route;
    }
    if (!$oltId) {
        return response()->json(['status' => 'error', 'message' => 'OLT ID is required'], 400);
    }

    $pythonBinary = '/var/www/html/olt_project/venv/bin/python';
    $scriptPath   = '/var/www/html/olt_project/' . $scriptName;

    // Set process timeout to 180 seconds (3 minutes)
    $process = Process::path('/var/www/html/olt_project')
        ->timeout(380)
        ->run("{$pythonBinary} {$scriptPath} {$oltId}");

    if ($process->failed()) {
        $rawError = $process->errorOutput();
        if (empty(trim($rawError))) {
            $rawError = $process->output();
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Python Script Error',
            'detail'  => !empty(trim($rawError)) ? $rawError : 'Process exited with error code ' . $process->exitCode()
        ], 500);
    }

    $output = json_decode($process->output(), true) ?? $process->output();

    return response()->json([
        'status' => 'success',
        'data'   => $output
    ]);
}
public function liveWalk(Request $request)
{
    $request->validate([
        'olt_id'    => 'required',
        'olt_ip'    => 'required|string',
        'community' => 'required|string',
        'oids'      => 'required|array|min:1',
        'oids.*.column' => 'required|string',
        'oids.*.label'  => 'required|string',
        'oids.*.oid'    => 'required|string',
    ]);

    $ip        = $request->input('olt_ip');
    $community = $request->input('community');
    $oids      = $request->input('oids');

    // Basic IP safety
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Invalid OLT IP address'
        ], 400);
    }

    $results = [];

    foreach ($oids as $item) {
        $oid    = trim($item['oid']);
        $column = $item['column'];
        $label  = $item['label'];

        // Skip empty / NULL
        if ($oid === '' || strtoupper($oid) === 'NULL') {
            $results[] = [
                'column' => $column,
                'label'  => $label,
                'oid'    => $oid,
                'error'  => 'OID is empty or NULL',
            ];
            continue;
        }

        // Run snmpwalk (SNMPv2c)
        // -v2c : version 2c
        // -c   : community
        // -O n : numeric OIDs (cleaner)
        // -t 3 : timeout 3s per request
        // -r 1 : 1 retry
        $cmd = sprintf(
            'snmpwalk -v2c -c %s -O n -t 5 -r 1 %s %s 2>&1',
            escapeshellarg($community),
            escapeshellarg($ip),
            escapeshellarg($oid)
        );

        $output    = [];
        $exitCode  = 0;
        exec($cmd, $output, $exitCode);

        $outputText = implode("\n", $output);

        if ($exitCode !== 0 || stripos($outputText, 'Timeout') !== false || stripos($outputText, 'No Such') !== false) {
            $results[] = [
                'column' => $column,
                'label'  => $label,
                'oid'    => $oid,
                'error'  => $outputText ?: 'snmpwalk failed (exit code ' . $exitCode . ')',
                'output' => null,
            ];
        } else {
            $results[] = [
                'column' => $column,
                'label'  => $label,
                'oid'    => $oid,
                'error'  => null,
                'output' => $outputText ?: '(no data returned)',
            ];
        }
    }

    return response()->json([
        'status' => 'success',
        'data'   => $results,
    ]);
}
}