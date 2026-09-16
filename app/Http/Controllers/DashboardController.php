<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OltConfig;
use App\Models\OltInformation;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
class DashboardController extends Controller
{
    public function index()
{
    // 1. Active vs Inactive OLT Count
    $activeCount = OltConfig::where('sts', 'active')->count();
    $inactiveCount = OltConfig::where('sts', '!=', 'active')->count();

    // 2. Group by Brand wise OLT count
    $brandCounts = OltConfig::select('olt_brand', DB::raw('count(*) as total'))
        ->groupBy('olt_brand')
        ->get();

    // 3. Group by Type wise OLT count
    $typeCounts = OltConfig::select('type', DB::raw('count(*) as total'))
        ->groupBy('type')
        ->get();

    // --- Overall ONU Metrics Processing ---
    $onuQuery = OltInformation::where('sys_port', 'LIKE', '%/%')
        ->where('sys_port', 'LIKE', '%:%');

    $totalOnus = (clone $onuQuery)->count();

    $onlineOnus = (clone $onuQuery)
        ->where('sys_sts', '1')
        ->whereRaw("router_mac REGEXP '^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$|[0-9A-Fa-f]{12}$|([0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4})$'")
        ->count();

    $offlineOnus = max(0, $totalOnus - $onlineOnus);

    // --- Active OLT Name-wise ONU Summary ---
    $oltTable = (new OltConfig)->getTable();
    $onuTable = (new OltInformation)->getTable();

    $oltNameColumn = 'olt_name'; 

    $oltOnuSummaries = DB::table("{$oltTable} as olt")
        ->leftJoin("{$onuTable} as onu", function ($join) {
            $join->on('olt.id', '=', 'onu.sys_gen_id')
                 ->where('onu.sys_port', 'LIKE', '%/%')
                 ->where('onu.sys_port', 'LIKE', '%:%');
        })
        ->where('olt.sts', 'active')
        ->select(
            'olt.id',
            "olt.{$oltNameColumn} as olt_name",
            'olt.snmpsts',
            'olt.sshTElnetsts',
            'olt.details_snmp',
            'olt.details_sshtelnet',
            'olt.olt_brand',
            'olt.type',
            'olt.typeconnection',
            DB::raw('COUNT(onu.id) as total_onu'),
            DB::raw("SUM(
                CASE 
                    WHEN onu.sys_sts = '1' 
                    AND onu.router_mac REGEXP '^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$|[0-9A-Fa-f]{12}$|([0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4})$' 
                    THEN 1 ELSE 0 
                END
            ) as online_onu")
        )
        ->groupBy(
            'olt.id', 
            "olt.{$oltNameColumn}",
            'olt.snmpsts',
            'olt.sshTElnetsts',
            'olt.details_snmp',
            'olt.details_sshtelnet',
            'olt.olt_brand',
            'olt.type',
            'olt.typeconnection'
        )
        ->get()
      ->map(function ($item) {
    $item->total_onu = (int) $item->total_onu;
    $item->online_onu = (int) $item->online_onu;
    $item->offline_onu = max(0, $item->total_onu - $item->online_onu);

    // Normalize SNMP Status & Alert
    $snmpSts = strtolower((string)$item->snmpsts);
    if (strpos($snmpSts, 'store_failed') !== false) {
        $item->snmp_alert = 'warning';
        $item->snmp_badge = 'Warning (Store Failed)';
    } elseif (strpos($snmpSts, 'failed') !== false) {
        $item->snmp_alert = 'danger';
        $item->snmp_badge = 'Connection Failed';
    } else {
        $item->snmp_alert = 'success';
        $item->snmp_badge = 'OK';
    }

    // Normalize SSH/Telnet Status & Alert
    $sshSts = strtolower((string)$item->sshTElnetsts);
    if (strpos($sshSts, 'store_failed') !== false) {
        $item->ssh_alert = 'warning';
        $item->ssh_badge = 'Warning (Store Failed)';
    } elseif (strpos($sshSts, 'failed') !== false) {
        $item->ssh_alert = 'danger';
        $item->ssh_badge = 'Connection Failed';
    } else {
        $item->ssh_alert = 'success';
        $item->ssh_badge = 'OK';
    }

    return $item;
});

    // --- Aggregated Alert Summary Statistics (Overall Counts) ---
    $statusSummary = [
        'snmp' => [
            'success'      => OltConfig::where('sts', 'active')->where('snmpsts', 'success')->count(),
            'store_failed' => OltConfig::where('sts', 'active')->where('snmpsts', 'success,Store_failed')->count(),
            'failed'       => OltConfig::where('sts', 'active')->where('snmpsts', 'failed')->count(),
        ],
        'ssh_telnet' => [
            'success'      => OltConfig::where('sts', 'active')->where('sshTElnetsts', 'success')->count(),
            'store_failed' => OltConfig::where('sts', 'active')->where('sshTElnetsts', 'success,Store_failed')->count(),
            'failed'       => OltConfig::where('sts', 'active')->where('sshTElnetsts', 'failed')->count(),
        ]
    ];

    return view('dashboard', compact(
        'activeCount',
        'inactiveCount',
        'brandCounts',
        'typeCounts',
        'totalOnus',
        'onlineOnus',
        'offlineOnus',
        'oltOnuSummaries',
        'statusSummary'
    ));
}
    public function getMetrics()
{
    $metrics = DB::table('oltdatatablepresent')
        ->where('sys_port', 'LIKE', '%:%') // <--- Add this filter to align with loss_list
        ->selectRaw("
            COUNT(*) as ports,
            SUM(CASE WHEN (sys_tx - sys_rx) > 239 THEN 1 ELSE 0 END) as `24plusDB`,
            SUM(CASE WHEN sys_rx = 'N/A' AND sys_sts = 2 THEN 1 ELSE 0 END) as offlineonu,
            SUM(CASE WHEN (sys_rx > 0 OR sys_rx < 0) AND sys_sts = 1 THEN 1 ELSE 0 END) as onlineonu,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 240 AND 249 THEN 1 ELSE 0 END) as `24db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 250 AND 259 THEN 1 ELSE 0 END) as `25db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 260 AND 269 THEN 1 ELSE 0 END) as `26db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 270 AND 279 THEN 1 ELSE 0 END) as `27db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 280 AND 289 THEN 1 ELSE 0 END) as `28db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 290 AND 299 THEN 1 ELSE 0 END) as `29db`,
            SUM(CASE WHEN (sys_tx - sys_rx) BETWEEN 300 AND 309 THEN 1 ELSE 0 END) as `30db`,
            SUM(CASE WHEN (sys_tx - sys_rx) >= 310 THEN 1 ELSE 0 END) as `31dbp`
        ")
        ->first();

    return response()->json([
        '24db' => $metrics->{'24db'} ?? 0,
        '25db' => $metrics->{'25db'} ?? 0,
        '26db' => $metrics->{'26db'} ?? 0,
        '27db' => $metrics->{'27db'} ?? 0,
        '28db' => $metrics->{'28db'} ?? 0,
        '29db' => $metrics->{'29db'} ?? 0,
        '30db' => $metrics->{'30db'} ?? 0,
        '31dbp' => $metrics->{'31dbp'} ?? 0,
    ]);
}
public function getLossList(Request $request)
{
    $request->validate([
        'lossidfirst' => 'required|integer',
        'lossidlast' => 'nullable|integer',
    ]);

    $dbfr = $request->input('lossidfirst');
    $dblast = $request->input('lossidlast');

    $query = DB::table('oltdatatablepresent');

    // Filter by port format if applied in metrics
    $query->where('sys_port', 'LIKE', '%:%');

    // Handle open-ended "24+" range vs fixed ranges (e.g. 240 to 249)
    if ($dblast === null || $dblast == 0 || $dblast == '') {
        // Range 24+ means everything >= 240
        $query->whereRaw('(`sys_tx` - `sys_rx`) >= ?', [$dbfr]);
    } else {
        // Fixed range (e.g., 240 to 249)
        $query->whereRaw('(`sys_tx` - `sys_rx`) BETWEEN ? AND ?', [$dbfr, $dblast]);
    }

    $records = $query->orderBy('sys_from', 'asc')->get();

    return view('components.loss_table', compact('records'));
}


public function search(Request $request)
{
    try {
        $inputMac = trim($request->input('mac'));

        if (empty($inputMac)) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid MAC address.'], 400);
        }

        $cleanMac = strtoupper(preg_replace('/[^a-fA-F0-9]/', '', $inputMac));

        if (strlen($cleanMac) < 6) {
            return response()->json(['success' => false, 'message' => 'Invalid MAC address format.'], 400);
        }

        $formattedMacWithColons = implode(':', str_split($cleanMac, 2));
        $prefix = substr($cleanMac, 0, 6);

        // Fetch Vendor Info with fallback on API timeout
        $vendorData = Cache::remember("mac_vendor_{$prefix}", 60 * 24 * 30, function () use ($cleanMac) {
            try {
                $response = Http::timeout(3)->get("https://api.maclookup.app/v2/macs/{$cleanMac}");
                return $response->successful() ? $response->json() : null;
            } catch (\Exception $e) {
                return null;
            }
        });

        // Search local database
   $localMatches = DB::table('oltdatatablepresent')
    ->where(function ($query) use ($cleanMac, $formattedMacWithColons) {
        $query->where('sys_mac', 'LIKE', "%{$cleanMac}%")
              ->orWhere('sys_mac', 'LIKE', "%{$formattedMacWithColons}%")
              ->orWhere('router_mac', 'LIKE', "%{$cleanMac}%")
              ->orWhere('router_mac', 'LIKE', "%{$formattedMacWithColons}%");
    })
    ->select('id', 'sys_mac', 'router_mac', 'sys_port', 'sys_device', 'sys_from', 'sys_sts', 'sys_rx', 'sys_tx')
    ->get();
        return response()->json([
            'success' => true,
            'searched_mac' => $formattedMacWithColons,
            'vendor' => [
                'found' => $vendorData['found'] ?? false,
                'company' => $vendorData['company'] ?? 'Unknown Vendor',
                'prefix' => $vendorData['macPrefix'] ?? $prefix,
                'is_private' => ($vendorData['isPrivate'] ?? false) ? 'Yes' : 'No',
                'country' => $vendorData['country'] ?? 'N/A',
                'address' => $vendorData['address'] ?? 'N/A',
                'block_type' => $vendorData['blockType'] ?? 'MA-L',
                'block_start' => $vendorData['blockStart'] ?? 'N/A',
                'block_end' => $vendorData['blockEnd'] ?? 'N/A',
                'updated' => $vendorData['updated'] ?? 'N/A',
            ],
            'local_records' => $localMatches
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}
}