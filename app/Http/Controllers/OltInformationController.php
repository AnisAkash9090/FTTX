<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\OltInformation; // Make sure to import your model!
use App\Models\OltConfig;
use App\Models\olt_oid;
use Illuminate\Validation\Rule;
class OltInformationController extends Controller
{


private function getOidMatrix()
{
    return [
        'epon_BDCOM' => [
            'port_names' => '1.3.6.1.4.1.3320.9.64.4.1.1.2', 'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5', 'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',     'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.8',          'lcv'       => '1.3.6.1.2.1.2.2.1.9',        'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11', 'onumod'  => '1.3.6.1.4.1.3320.101.10.1.1.2', 'mac_get' => '1.3.6.1.4.1.3320.101.10.1.1.3',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27', 'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'gpon_BDCOM' => [
            'port_names' => '1.3.6.1.4.1.3320.9.64.4.1.1.2', 'tx_values' => '1.3.6.1.4.1.3320.10.3.4.1.3', 'alterTX' => '1.3.6.1.4.1.3320.10.3.4.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.10.3.4.1.2',  'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',     'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.8',          'lcv'       => '1.3.6.1.2.1.2.2.1.9',        'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.10.3.1.1.35', 'onumod'    => '1.3.6.1.4.1.3320.10.3.1.1.9', 'mac_get' => '1.3.6.1.4.1.3320.10.3.1.1.4',
            'onu_dist'   => '1.3.6.1.4.1.3320.10.3.1.1.33', 'onuvendor'=> '1.3.6.1.4.1.3320.10.3.1.1.2.'
        ],
        'epon_vsol' => [
            'port_names' => '1.3.6.1.4.1.37950.1.1.5.10.3.2.1.5', 'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5',     'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.7',              'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11',     'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.2.1.2.2.1.6',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27',     'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'gpon_vsol' => [
            'port_names' => '1.3.6.1.2.1.31.1.1.1.1',        'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5', 'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.7',          'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11', 'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.2.1.2.2.1.6',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27', 'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'epon_Huawei' => [
            'port_names' => '1.3.6.1.4.1.37950.1.1.5.10.3.2.1.5', 'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5',     'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.7',              'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11',     'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.2.1.2.2.1.6',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27',     'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'gpon_Huawei' => [
            'port_names' => '1.3.6.1.4.1.37950.1.1.5.10.3.2.1.5', 'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5',     'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.7',              'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11',     'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.2.1.2.2.1.6',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27',     'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'gpon_cdata' => [
            'port_names' => '1.3.6.1.4.1.37950.1.1.5.10.3.2.1.5', 'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5',     'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.7',              'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11',     'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.2.1.2.2.1.6',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27',     'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
        'epon_cdata' => [
            'port_names' => '1.3.6.1.2.1.31.1.1.1.1',        'tx_values' => '1.3.6.1.4.1.3320.101.10.5.1.6', 'alterTX' => '1.3.6.1.4.1.3320.101.107.1.3',
            'rx_values'  => '1.3.6.1.4.1.3320.101.10.5.1.5', 'up_value'  => '1.3.6.1.2.1.31.1.1.1.6',         'down_values' => '1.3.6.1.2.1.31.1.1.1.10',
            'sts'        => '1.3.6.1.2.1.2.2.1.8',          'lcv'       => '1.3.6.1.2.1.2.2.1.9',            'sn_values' => null,
            'reason'     => '1.3.6.1.4.1.3320.101.11.1.1.11', 'onumod'    => '1.3.6.1.4.1.3320.101.10.1.1.2',  'mac_get' => '1.3.6.1.4.1.3320.101.10.1.1.3',
            'onu_dist'   => '1.3.6.1.4.1.3320.101.10.1.1.27', 'onuvendor'=> '1.3.6.1.4.1.3320.101.10.1.1.1'
        ],
    ];
}

private function getOidsForKey($type, $brand)
{
    $key = $type . '_' . $brand;
    $matrix = $this->getOidMatrix();

    return $matrix[$key] ?? [
        'port_names' => null, 'tx_values' => null, 'alterTX' => null, 'rx_values' => null,
        'up_value' => null, 'down_values' => null, 'sts' => '1', 'lcv' => null,
        'sn_values' => null, 'reason' => null, 'onumod' => null, 'mac_get' => null,
        'onu_dist' => null, 'onuvendor' => null
    ];
}
    // This name MUST match the second part of your Route array
public function store(Request $request)
{
    $userId = \Illuminate\Support\Facades\Auth::user()->id;

    $validatedData = $request->validate([
        'olt_name'      => 'required|string|max:255',
        'olt_ip'        => 'required|ip|unique:olt_information,olt_ip',
        'olt_community' => 'required|string|max:255',
        'olt_type'      => 'required|string|in:epon,gpon',
        'olt_txrx'      => 'required|integer|in:0,1',
        'olt_interface' => 'required|integer|between:1,16',
        'olt_access'    => 'nullable|string|in:ssh,telnet',
        'stusername'    => 'nullable|string|max:255',
        'oltpass'       => 'nullable|string|max:255',
        'olt_brand'     => 'required|string|in:BDCOM,vsol,cdata,Huawei,ZTE,FiberHome,Raisecom',
    ], [
        'olt_ip.unique' => 'The IP address is already available.',
        'olt_ip.ip'     => 'Please provide a valid IP address.'
    ]);

    DB::beginTransaction();
    try {
        // 1. Insert into olt_information
        $olt = OltConfig::create([
            'user_name'      => $validatedData['olt_name'],
            'olt_name'       => $validatedData['olt_name'],
            'olt_ip'         => $validatedData['olt_ip'],
            'olt_community'  => $validatedData['olt_community'],
            'type'           => $validatedData['olt_type'],
            'txrxcmd'        => $validatedData['olt_txrx'],
            'port'           => $validatedData['olt_interface'],
            'typeconnection' => $validatedData['olt_access'],
            'useradmin'      => $validatedData['stusername'],
            'pass'           => $validatedData['oltpass'],
            'olt_brand'      => $validatedData['olt_brand'],
            'sts'            => 'active',
            'createinfo'     => $userId
        ]);

        // 2. Look up and save matching OID mapping
        $oids = $this->getOidsForKey($validatedData['olt_type'], $validatedData['olt_brand']);
        $oids['assign_for'] = $olt->id;
        olt_oid::create($oids);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'OLT details and template configurations successfully mapped.'
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to save configuration: ' . $e->getMessage()
        ], 500);
    }
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'olt_name'       => 'required|string|max:255',
        'olt_ip'         => [
            'required',
            'ip',
            Rule::unique('olt_information', 'olt_ip')->ignore($id)
        ],
        'olt_community'  => 'required|string|max:255',
        'typeconnection' => 'required|string|in:ssh,telnet,snmp',
        'useradmin'      => 'nullable|string|max:255',
        'pass'           => 'nullable|string|max:255',
        'type'           => 'required|string|in:epon,gpon',
        'olt_brand'      => 'required|string|in:BDCOM,vsol,cdata,Huawei,ZTE,FiberHome,Raisecom',
        'port'           => 'nullable|integer|min:1',
        'txrxcmd'        => 'nullable|boolean',
        'sts'            => 'nullable|string|in:active,inactive',
    ], [
        'olt_ip.unique'  => 'The IP address is already in use.',
        'olt_ip.ip'      => 'Please provide a valid IP address.'
    ]);

    DB::beginTransaction();
    try {
        $olt = OltConfig::findOrFail($id);

        // Check if technology type or brand changed to trigger OID update
        $typeOrBrandChanged = ($olt->type !== $validated['type'])
                           || ($olt->olt_brand !== $validated['olt_brand']);

        $olt->update([
            'user_name'      => $validated['olt_name'],
            'olt_name'       => $validated['olt_name'],
            'olt_ip'         => $validated['olt_ip'],
            'olt_community'  => $validated['olt_community'],
            'typeconnection' => $validated['typeconnection'],
            'useradmin'      => $validated['useradmin'] ?? null,
            'pass'           => $validated['pass'] ?? null,
            'type'           => $validated['type'],
            'olt_brand'      => $validated['olt_brand'],
            'port'           => $validated['port'] ?? 4,
            'txrxcmd'        => $request->has('txrxcmd') ? 1 : 0,
            'sts'            => $validated['sts'] ?? 'active',
        ]);

        if ($typeOrBrandChanged) {
            $oids = $this->getOidsForKey($validated['type'], $validated['olt_brand']);
            $oids['assign_for'] = $olt->id;

            olt_oid::updateOrCreate(
                ['assign_for' => $olt->id],
                $oids
            );
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'OLT updated successfully.' . ($typeOrBrandChanged ? ' OID mapping refreshed.' : '')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage()
        ], 500);
    }
}
public function toggleStatus(Request $request, $id)
{
    $validated = $request->validate([
        'sts' => 'required|string|in:active,inactive',
    ]);

    try {
        $olt = OltConfig::findOrFail($id);
        $olt->sts = $validated['sts'];
        $olt->save();

        return response()->json([
            'success' => true,
            'message' => 'OLT marked as ' . $validated['sts'] . '.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Status update failed: ' . $e->getMessage()
        ], 500);
    }
}

    public function olt()
    {
        $olts = OltConfig::all();
        
        // Ensure this view file exists at resources/views/olt_view.blade.php
        return view('oltView', compact('olts'));
    }
        public function oltadd()
    {
        $olts = OltConfig::all();
        
        // Ensure this view file exists at resources/views/olt_view.blade.php
        return view('oltAdd', compact('olts'));
    }
public function search(Request $request)
{
    $olts = OltConfig::where('sts', 'active')->get();
    
    $OLT_ID = $request->input('oltID');
    $showponsend = $request->input('showponsend');

    $results = collect();
    
    // Initialize default summary metrics counters
    $uponu = 0;
    $downonu = 0;
    $good = 0;
    $warn = 0;
    $bad = 0;

    if ($OLT_ID) {
        // --- 1. Fetch Main Data Grid Results ---
        $query = DB::table('oltdatatablepresent')->where('sys_gen_id', $OLT_ID);
        if ($showponsend && $showponsend !== 'All') {
            $query->where('sys_port', 'LIKE', $showponsend . ':%');
        }
        $results = $query->orderBy('sys_port')
                 ->paginate(100);

        // --- 2. Calculate Dashboard Summary Graph Counters (Up / Down) ---
        $ponPattern = ($showponsend && $showponsend !== 'All') ? $showponsend : '';
        
        $summary = DB::table('oltdatatablepresent')
            ->where('sys_gen_id', $OLT_ID)
            ->when($ponPattern, function($q) use ($ponPattern) {
                return $q->where('sys_port', 'LIKE', $ponPattern . ':%');
            })
            ->select([
                DB::raw("SUM(CASE WHEN sys_sts = 1 THEN 1 ELSE 0 END) as up_count"),
                DB::raw("SUM(CASE WHEN sys_sts = 2 THEN 1 ELSE 0 END) as down_count")
            ])->first();

        $uponu = $summary->up_count ?? 0;
        $downonu = $summary->down_count ?? 0;

        // --- 3. Calculate RX Ranges Loop Counter ---
  foreach ($results as $row) {
    // Explicitly confirm the database value is numeric before evaluating ranges
    if (isset($row->sys_rx) && is_numeric($row->sys_rx)) {
        $rxraw = intval($row->sys_rx);
        
        // GOOD: -15.0 to -26.9
        if ($rxraw <= -150 && $rxraw >= -269) {
            $good++;
        } 
        // WARN: -27.0 to -27.9
        else if ($rxraw <= -270 && $rxraw >= -279) {
            $warn++;
        } 
        // BAD: Too Hot or Too Weak
        else if (($rxraw <= 0 && $rxraw >= -149) || $rxraw <= -280) {
            $bad++;
        }
    } else {
        $bad++; // Count N/A or empty elements safely into bad/disconnected status category
    }
}
    }

    return view('oltView', compact('olts', 'OLT_ID', 'showponsend', 'results', 'uponu', 'downonu', 'good', 'warn', 'bad'));
}
public function getUniquePorts(Request $request)
    {
        $oltid = $request->query('oltid');
        $showponsend = $request->query('selectedPON', '');

        if (!$oltid) {
            return response('<option value="">Select OLT first...</option>');
        }

        // Query ports mapped under your table relation
        $ports = DB::table('oltdatatablepresent')
            ->where('sys_gen_id', $oltid)
            ->orderBy('sys_port')
            ->pluck('sys_port');

        if ($ports->isEmpty()) {
            return response('<option value="">No PON found</option>');
        }

        $uniquePorts = [];
        $htmlOutput = '<option value="All">All</option>';

        foreach ($ports as $sysPort) {
            // Replicates php explode(':', $sysPort)[0] cleanly
            $port = head(explode(':', $sysPort));

            if (!in_array($port, $uniquePorts) && !empty($port)) {
                $uniquePorts[] = $port;
                $selected = ($port === $showponsend) ? 'selected' : '';
                $htmlOutput .= '<option value="' . e($port) . '" ' . $selected . '>' . e($port) . '</option>';
            }
        }

        return response($htmlOutput);
    }

 public function olt2(Request $request)
{
    $oltId     = $request->input('oltID');
    $ponFilter = $request->input('showponsend');

    $olts = OltConfig::where('sts', 'active')->get();

    $data  = null;
    $stats = ['up'=>0,'down'=>0,'good'=>0,'warn'=>0,'bad'=>0];

    if ($request->has('subOLT') && $oltId) {

        $deviceInfo = OltInformation::where('sys_gen_id', $oltId)
            ->latest()
            ->first();

        $query = OltInformation::where('sys_gen_id', $oltId);

        if ($ponFilter && $ponFilter !== 'All') {
            $query->where('sys_port', 'LIKE', "$ponFilter:%");
        }

        $tableData = $query->orderBy('sys_port')->get();

        foreach ($tableData as $row) {

            if ($row->sys_sts == '1') $stats['up']++;
            else $stats['down']++;

            if (is_numeric($row->sys_rx)) {
                $rx = (int) $row->sys_rx;

                if ($rx <= -150 && $rx >= -269) $stats['good']++;
                elseif ($rx <= -270 && $rx >= -279) $stats['warn']++;
                else $stats['bad']++;
            }
        }

        $data = [
            'device' => $deviceInfo,
            'table'  => $tableData,
            'stats'  => $stats,
            'config' => OltConfig::find($oltId)
        ];
    }

    return view('oltView', compact(
        'olts',
        'oltId',
        'ponFilter',
        'data'
    ));
} 



}