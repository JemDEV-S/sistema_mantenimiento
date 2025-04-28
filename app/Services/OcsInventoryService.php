<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Asset;
use App\Models\Department;
use Carbon\Carbon;

class OcsInventoryService
{
    /**
     * Get all equipment from OCS Inventory
     */
    public function getAllEquipment()
    {
        return DB::connection('ocs_inventory')
            ->table('hardware')
            ->select(
                'hardware.ID as id',
                'hardware.NAME as name',
                'hardware.LASTCOME as last_access',
                'hardware.LASTDATE as last_update',
                'hardware.DESCRIPTION as description',
                'hardware.OSNAME as operating_system',
                'bios.SSN as serial',
                'bios.SMANUFACTURER as manufacturer',
                'bios.SMODEL as model'
            )
            ->leftJoin('bios', 'hardware.ID', '=', 'bios.HARDWARE_ID')
            ->get();
    }
    
    /**
     * Search equipment by name
     */
    public function searchEquipmentByName($name)
    {
        return DB::connection('ocs_inventory')
            ->table('hardware')
            ->select(
                'hardware.ID as id',
                'hardware.NAME as name',
                'hardware.LASTCOME as last_access',
                'hardware.DESCRIPTION as description',
                'bios.SSN as serial',
                'bios.SMANUFACTURER as manufacturer',
                'bios.SMODEL as model'
            )
            ->leftJoin('bios', 'hardware.ID', '=', 'bios.HARDWARE_ID')
            ->where('hardware.NAME', 'like', "%{$name}%")
            ->get();
    }
    
    /**
     * Get details of a specific equipment
     */
    public function getEquipmentDetails($id)
    {
        // Basic hardware information
        $equipment = DB::connection('ocs_inventory')
            ->table('hardware')
            ->select(
                'hardware.ID as id',
                'hardware.NAME as name',
                'hardware.LASTCOME as last_access',
                'hardware.LASTDATE as last_update',
                'hardware.DESCRIPTION as description',
                'hardware.OSNAME as operating_system',
                'hardware.OSVERSION as os_version',
                'hardware.PROCESSORT as processor',
                'hardware.PROCESSORN as processor_count',
                'hardware.MEMORY as memory',
                'bios.SSN as serial',
                'bios.BMANUFACTURER as bios_manufacturer',
                'bios.BVERSION as bios_version',
                'bios.SMANUFACTURER as manufacturer',
                'bios.SMODEL as model'
            )
            ->leftJoin('bios', 'hardware.ID', '=', 'bios.HARDWARE_ID')
            ->where('hardware.ID', $id)
            ->first();
            
        if (!$equipment) {
            return null;
        }
        
        // Storage information
        $disks = DB::connection('ocs_inventory')
            ->table('storages')
            ->select('NAME', 'MANUFACTURER', 'MODEL', 'DESCRIPTION', 'TYPE', 'DISKSIZE')
            ->where('HARDWARE_ID', $id)
            ->get();
            
        // Network information
        $networks = DB::connection('ocs_inventory')
            ->table('networks')
            ->select('DESCRIPTION', 'TYPE', 'IPADDRESS', 'IPMASK', 'IPGATEWAY', 'MACADDR', 'STATUS')
            ->where('HARDWARE_ID', $id)
            ->get();
            
        // Installed software information
        $software = DB::connection('ocs_inventory')
            ->table('software')
            ->select('NAME_ID', 'PUBLISHER_ID', 'VERSION_ID', 'FOLDER', 'COMMENTS', 'INSTALLDATE')
            ->where('HARDWARE_ID', $id)
            ->get();
            
        // Monitor information
        $monitors = DB::connection('ocs_inventory')
            ->table('monitors')
            ->select('MANUFACTURER', 'CAPTION', 'DESCRIPTION', 'TYPE', 'SERIAL')
            ->where('HARDWARE_ID', $id)
            ->get();
            
        // Printer information
        $printers = DB::connection('ocs_inventory')
            ->table('printers')
            ->select('NAME', 'DRIVER', 'PORT', 'DESCRIPTION')
            ->where('HARDWARE_ID', $id)
            ->get();
            
        // Merge all information
        $equipment->disks = $disks;
        $equipment->networks = $networks;
        $equipment->software = $software;
        $equipment->monitors = $monitors;
        $equipment->printers = $printers;
        
        return $equipment;
    }
    
    /**
     * Synchronize assets from OCS Inventory to local database
     */
    public function syncAssets()
    {
        $ocsEquipment = $this->getAllEquipment();
        $counter = 0;
        
        foreach ($ocsEquipment as $ocsEquip) {
            // Check if asset already exists
            $asset = Asset::where('ocs_id', $ocsEquip->id)->first();
            
            // Determine asset type based on characteristics
            $type = $this->determineAssetType($ocsEquip);
            
            // Assign department (in a real system, this could be based on more complex rules)
            $departmentId = Department::first()->id ?? 1;
            
            if ($asset) {
                // Update existing asset
                $asset->update([
                    'name' => $ocsEquip->name,
                    'type' => $type,
                    'brand' => $ocsEquip->manufacturer ?? 'Unknown',
                    'model' => $ocsEquip->model ?? 'Unknown',
                    'serial' => $ocsEquip->serial ?? 'Unknown',
                    'last_sync' => Carbon::now(),
                ]);
            } else {
                // Create new asset
                Asset::create([
                    'ocs_id' => $ocsEquip->id,
                    'name' => $ocsEquip->name,
                    'type' => $type,
                    'brand' => $ocsEquip->manufacturer ?? 'Unknown',
                    'model' => $ocsEquip->model ?? 'Unknown',
                    'serial' => $ocsEquip->serial ?? 'Unknown',
                    'department_id' => $departmentId,
                    'status' => 'active',
                    'last_sync' => Carbon::now(),
                ]);
                
                $counter++;
            }
        }
        
        return $counter;
    }
    
    /**
     * Determine asset type based on characteristics
     */
    private function determineAssetType($ocsEquip)
    {
        $name = strtolower($ocsEquip->name);
        $description = strtolower($ocsEquip->description ?? '');
        $model = strtolower($ocsEquip->model ?? '');
        
        if (str_contains($name, 'laptop') || str_contains($name, 'notebook') || 
            str_contains($description, 'laptop') || str_contains($description, 'portable') ||
            str_contains($model, 'laptop')) {
            return 'Laptop';
        }
        
        if (str_contains($name, 'desktop') || str_contains($name, 'pc') || 
            str_contains($description, 'desktop') || str_contains($description, 'workstation')) {
            return 'Desktop';
        }
        
        if (str_contains($name, 'server') || str_contains($description, 'server')) {
            return 'Server';
        }
        
        if (str_contains($name, 'printer') || str_contains($description, 'printer')) {
            return 'Printer';
        }
        
        if (str_contains($name, 'monitor') || str_contains($description, 'monitor') || 
            str_contains($description, 'display')) {
            return 'Monitor';
        }
        
        return 'Other';
    }
}