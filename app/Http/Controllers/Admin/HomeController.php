<?php

namespace App\Http\Controllers\Admin;

// GDPR
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Actor;
use App\Models\Annuaire;
use App\Models\Application;
use App\Models\ApplicationBlock;
use App\Models\ApplicationFlow;
use App\Models\ApplicationModule;
use App\Models\ApplicationService;
use App\Models\Bay;
use App\Models\Building;
use App\Models\Certificate;
use App\Models\Cluster;
use App\Models\Container;
use App\Models\Database;
use App\Models\DataProcessing;
use App\Models\DhcpServer;
use App\Models\Dnsserver;
use App\Models\Domain;
use App\Models\Entity;
use App\Models\ExternalConnectedEntity;
use App\Models\ForestAd;
use App\Models\Gateway;
use App\Models\Information;
use App\Models\Lan;
use App\Models\LogicalServer;
use App\Models\MacroProcessus;
use App\Models\Man;
use App\Models\Network;
use App\Models\NetworkSwitch;
use App\Models\Operation;
use App\Models\Peripheral;
use App\Models\Phone;
use App\Models\PhysicalRouter;
use App\Models\PhysicalSecurityDevice;
use App\Models\PhysicalServer;
use App\Models\PhysicalSwitch;
use App\Models\Process;
use App\Models\Relation;
use App\Models\Router;
use App\Models\SecurityControl;
use App\Models\SecurityDevice;
use App\Models\Site;
use App\Models\StorageDevice;
use App\Models\Subnetwork;
use App\Models\Task;
use App\Models\Vlan;
use App\Models\Wan;
use App\Models\WifiTerminal;
use App\Models\Workstation;
use App\Models\Zone;
use App\Models\ZoneAdmin;
// ecosystem
// information system
// Applications
// Administration
// Logique
// Physique

class HomeController extends Controller
{
    /**
     * Show maturity level 1.
     */
    public function maturity1(): \Illuminate\Contracts\Support\Renderable
    {
        $view = view('admin/reports/maturity1');
        foreach ($this->computeMaturity() as $maturity => $level) {
            $view = $view->with($maturity, $level);
        }

        return $view;
    }

    /**
     * Show maturity level 2.
     */
    public function maturity2(): \Illuminate\Contracts\Support\Renderable
    {
        $view = view('admin/reports/maturity2');
        foreach ($this->computeMaturity() as $maturity => $level) {
            $view = $view->with($maturity, $level);
        }

        return $view;
    }

    /**
     * Show maturity level 3.
     */
    public function maturity3(): \Illuminate\Contracts\Support\Renderable
    {
        $view = view('admin/reports/maturity3');
        foreach ($this->computeMaturity() as $maturity => $level) {
            $view = $view->with($maturity, $level);
        }

        return $view;
    }

    /**
     * Show the application dashboard.
     */
    public function index(): \Illuminate\Contracts\Support\Renderable
    {
        $view = view('home');
        foreach ($this->computeMaturity() as $maturity => $level) {
            $view = $view->with($maturity, $level);
        }

        return $view;
    }

    /**
     * Compute maturity levels
     */
    protected function computeMaturity(): array
    {
        $levels = [
            // GDPR
            'data_processing'  => DataProcessing::count(),
            'security_controls' => SecurityControl::count(),

            // Ecosystem
            'entities'      => Entity::count(),
            'entities_lvl1' => Entity::maturityLevel1()->count(),

            'relations'      => Relation::count(),
            'relations_lvl1' => Relation::maturityLevel1()->count(),
            'relations_lvl2' => Relation::maturityLevel2()->count(),

            // Information system
            'macroProcessuses'      => MacroProcessus::count(),
            'macroProcessuses_lvl2' => MacroProcessus::maturityLevel2()->count(),
            'macroProcessuses_lvl3' => MacroProcessus::maturityLevel3()->count(),

            'processes'      => Process::count(),
            'processes_lvl1' => Process::maturityLevel1()->count(),
            'processes_lvl2' => Process::maturityLevel2()->count(),

            'activities'      => Activity::count(),
            'activities_lvl2' => Activity::maturityLevel2()->count(),

            'operations'      => Operation::count(),
            'operations_lvl1' => Operation::maturityLevel1()->count(),
            'operations_lvl2' => Operation::maturityLevel2()->count(),
            'operations_lvl3' => Operation::maturityLevel3()->count(),

            'tasks'      => Task::count(),
            'tasks_lvl3' => Task::maturityLevel3()->count(),

            'actors'      => Actor::count(),
            'actors_lvl2' => Actor::maturityLevel2()->count(),

            'informations'      => Information::count(),
            'informations_lvl1' => Information::maturityLevel1()->count(),
            'informations_lvl2' => Information::maturityLevel2()->count(),

            // Applications
            'applicationBlocks'      => ApplicationBlock::count(),
            'applicationBlocks_lvl2' => ApplicationBlock::maturityLevel2()->count(),

            'applications'      => Application::count(),
            'applications_lvl1' => Application::maturityLevel1()->count(),
            'applications_lvl2' => Application::maturityLevel2()->count(),
            'applications_lvl3' => Application::maturityLevel3()->count(),

            'applicationServices'      => ApplicationService::count(),
            'applicationServices_lvl2' => ApplicationService::maturityLevel2()->count(),

            'applicationModules'      => ApplicationModule::count(),
            'applicationModules_lvl2' => ApplicationModule::maturityLevel2()->count(),

            'databases'      => Database::count(),
            'databases_lvl1' => Database::maturityLevel1()->count(),
            'databases_lvl2' => Database::maturityLevel2()->count(),

            'flows'      => ApplicationFlow::count(),
            'flows_lvl1' => ApplicationFlow::maturityLevel1()->count(),

            // Administration
            'zones_ad'      => ZoneAdmin::count(),
            'zones_ad_lvl1' => ZoneAdmin::maturityLevel1()->count(),

            'annuaires'      => Annuaire::count(),
            'annuaires_lvl1' => Annuaire::maturityLevel1()->count(),

            'forests'      => ForestAd::count(),
            'forests_lvl1' => ForestAd::maturityLevel1()->count(),

            'domains'        => Domain::count(),
            'domaines_lvl1'  => Domain::maturityLevel1()->count(),

            // Logique
            'networks'      => Network::count(),
            'networks_lvl1' => Network::maturityLevel1()->count(),

            'subnetworks'      => Subnetwork::count(),
            'subnetworks_lvl1' => Subnetwork::maturityLevel1()->count(),

            'gateways'      => Gateway::count(),
            'gateways_lvl1' => Gateway::maturityLevel1()->count(),

            'externalConnectedEntities'      => ExternalConnectedEntity::count(),
            'externalConnectedEntities_lvl2' => ExternalConnectedEntity::maturityLevel2()->count(),

            'switches'      => NetworkSwitch::count(),
            'switches_lvl1' => NetworkSwitch::maturityLevel1()->count(),

            'routers'      => Router::count(),
            'routers_lvl1' => Router::maturityLevel1()->count(),

            'securityDevices'      => SecurityDevice::count(),
            'securityDevices_lvl1' => SecurityDevice::maturityLevel1()->count(),

            'DHCPServers'      => DhcpServer::count(),
            'DHCPServers_lvl2' => DhcpServer::maturityLevel2()->count(),

            'DNSServers'      => Dnsserver::count(),
            'DNSServers_lvl2' => Dnsserver::maturityLevel2()->count(),

            'clusters'      => Cluster::count(),
            'clusters_lvl1' => Cluster::maturityLevel1()->count(),

            'logicalServers'      => LogicalServer::count(),
            'logicalServers_lvl1' => LogicalServer::maturityLevel1()->count(),

            'containers'      => Container::count(),
            'containers_lvl1' => Container::maturityLevel1()->count(),

            'certificates'      => Certificate::count(),
            'certificates_lvl2' => Certificate::maturityLevel2()->count(),

            // Physical
            'sites'      => Site::count(),
            'sites_lvl1' => Site::maturityLevel1()->count(),

            'buildings'      => Building::count(),
            'buildings_lvl1' => Building::maturityLevel1()->count(),

            'bays'      => Bay::count(),
            'bays_lvl1' => Bay::maturityLevel1()->count(),

            'zones' => Zone::count(),

            'physicalServers'      => PhysicalServer::count(),
            'physicalServers_lvl1' => PhysicalServer::maturityLevel1()->count(),

            'workstations'      => Workstation::count(),
            'workstations_lvl1' => Workstation::maturityLevel1()->count(),

            'storageDevices'      => StorageDevice::count(),
            'storageDevices_lvl1' => StorageDevice::maturityLevel1()->count(),

            'peripherals'      => Peripheral::count(),
            'peripherals_lvl1' => Peripheral::maturityLevel1()->count(),

            'phones'      => Phone::count(),
            'phones_lvl1' => Phone::maturityLevel1()->count(),

            'physicalSwitchs'      => PhysicalSwitch::count(),
            'physicalSwitchs_lvl1' => PhysicalSwitch::maturityLevel1()->count(),

            'physicalRouters'      => PhysicalRouter::count(),
            'physicalRouters_lvl1' => PhysicalRouter::maturityLevel1()->count(),

            'wifiTerminals'      => WifiTerminal::count(),
            'wifiTerminals_lvl1' => WifiTerminal::maturityLevel1()->count(),

            'physicalSecurityDevices'      => PhysicalSecurityDevice::count(),
            'physicalSecurityDevices_lvl1' => PhysicalSecurityDevice::maturityLevel1()->count(),

            'wans'      => ($wan_count = Wan::count()),
            'wans_lvl1' => $wan_count,

            'mans'      => ($man_count = Man::count()),
            'mans_lvl1' => $man_count,

            'lans'      => Lan::count(),
            'lans_lvl1' => Lan::maturityLevel1()->count(),

            'vlans'      => Vlan::count(),
            'vlans_lvl1' => Vlan::maturityLevel1()->count(),
        ];

        // Maturity Level 1
        $denominator =
            $levels['entities'] + $levels['relations'] +
            $levels['processes'] + $levels['operations'] + $levels['informations'] +
            $levels['applications'] + $levels['databases'] + $levels['flows'] +
            $levels['zones_ad'] + $levels['annuaires'] + $levels['forests'] + $levels['domains'] +
            $levels['networks'] + $levels['subnetworks'] + $levels['gateways'] + $levels['switches'] + $levels['routers'] + $levels['securityDevices'] + $levels['clusters'] + $levels['logicalServers'] + $levels['containers'] +
            $levels['sites'] + $levels['buildings'] + $levels['bays'] + $levels['physicalServers'] + $levels['physicalRouters'] + $levels['physicalSwitchs'] + $levels['physicalSecurityDevices'] +
            $levels['wans'] + $levels['mans'] + $levels['lans'] + $levels['vlans'];

        $levels['maturity1'] =
            $denominator > 0 ?
            number_format(
                ($levels['entities_lvl1'] + $levels['relations_lvl1'] +
            $levels['processes_lvl1'] + $levels['operations_lvl1'] + $levels['informations_lvl1'] +
            $levels['applications_lvl1'] + $levels['databases_lvl1'] + $levels['flows_lvl1'] +
            $levels['zones_ad_lvl1'] + $levels['annuaires_lvl1'] + $levels['forests_lvl1'] + $levels['domaines_lvl1'] +
            $levels['networks_lvl1'] + $levels['subnetworks_lvl1'] + $levels['gateways_lvl1'] + $levels['switches_lvl1'] + $levels['routers_lvl1'] + $levels['securityDevices_lvl1'] + $levels['clusters_lvl1'] + $levels['logicalServers_lvl1'] + $levels['containers_lvl1'] +
            $levels['sites_lvl1'] + $levels['buildings_lvl1'] + $levels['bays_lvl1'] + $levels['physicalServers_lvl1'] + $levels['physicalRouters_lvl1'] + $levels['physicalSwitchs_lvl1'] + $levels['physicalSecurityDevices_lvl1'] +
            $levels['wans_lvl1'] + $levels['mans_lvl1'] + $levels['lans_lvl1'] + $levels['vlans_lvl1']) * 100 / $denominator,
                0
            ) : 0;

        // Maturity Level 2
        $denominator =
            $levels['entities'] + $levels['relations'] +
            $levels['macroProcessuses'] + $levels['processes'] + $levels['activities'] + $levels['operations'] + $levels['actors'] + $levels['informations'] +
            $levels['applicationBlocks'] + $levels['applications'] + $levels['applicationServices'] + $levels['applicationModules'] + $levels['databases'] + $levels['flows'] +
            $levels['zones_ad'] + $levels['annuaires'] + $levels['forests'] + $levels['domains'] +
            $levels['networks'] + $levels['subnetworks'] + $levels['gateways'] + $levels['externalConnectedEntities'] + $levels['switches'] + $levels['routers'] + $levels['securityDevices'] + $levels['DHCPServers'] + $levels['DNSServers'] + $levels['clusters'] + $levels['logicalServers'] + $levels['certificates'] + $levels['containers'] +
            $levels['sites'] + $levels['buildings'] + $levels['bays'] + $levels['physicalServers'] + $levels['physicalRouters'] + $levels['physicalSwitchs'] + $levels['physicalSecurityDevices'] +
            $levels['wans'] + $levels['mans'] + $levels['lans'] + $levels['vlans'];

        $levels['maturity2'] =
            $denominator > 0 ?
            number_format(
                ($levels['entities_lvl1'] + $levels['relations_lvl2'] +
            $levels['macroProcessuses_lvl2'] + $levels['processes_lvl2'] + $levels['activities_lvl2'] + $levels['operations_lvl2'] + $levels['actors_lvl2'] + $levels['informations_lvl2'] +
            $levels['applicationBlocks_lvl2'] + $levels['applications_lvl2'] + $levels['applicationServices_lvl2'] + $levels['applicationModules_lvl2'] + $levels['databases_lvl2'] + $levels['flows_lvl1'] +
            $levels['zones_ad_lvl1'] + $levels['annuaires_lvl1'] + $levels['forests_lvl1'] + $levels['domaines_lvl1'] +
            $levels['networks_lvl1'] + $levels['subnetworks_lvl1'] + $levels['gateways_lvl1'] + $levels['externalConnectedEntities_lvl2'] + $levels['switches_lvl1'] + $levels['routers_lvl1'] + $levels['securityDevices_lvl1'] + $levels['DHCPServers_lvl2'] + $levels['DNSServers_lvl2'] + $levels['clusters_lvl1'] + $levels['logicalServers_lvl1'] + $levels['containers_lvl1'] + $levels['certificates_lvl2'] +
            $levels['sites_lvl1'] + $levels['buildings_lvl1'] + $levels['bays_lvl1'] + $levels['physicalServers_lvl1'] + $levels['physicalRouters_lvl1'] + $levels['physicalSwitchs_lvl1'] + $levels['physicalSecurityDevices_lvl1'] +
            $levels['wans_lvl1'] + $levels['mans_lvl1'] + $levels['lans_lvl1'] + $levels['vlans_lvl1']) * 100 / $denominator,
                0
            ) : 0;

        // Maturity Level 3
        $denominator =
            $levels['entities'] + $levels['relations'] +
            $levels['macroProcessuses'] + $levels['processes'] + $levels['activities'] + $levels['tasks'] + $levels['operations'] + $levels['actors'] + $levels['informations'] +
            $levels['applicationBlocks'] + $levels['applications'] + $levels['applicationServices'] + $levels['applicationModules'] + $levels['databases'] + $levels['flows'] +
            $levels['zones_ad'] + $levels['annuaires'] + $levels['forests'] + $levels['domains'] +
            $levels['networks'] + $levels['subnetworks'] + $levels['gateways'] + $levels['externalConnectedEntities'] + $levels['switches'] + $levels['routers'] + $levels['securityDevices'] + $levels['DHCPServers'] + $levels['DNSServers'] + $levels['clusters'] + $levels['logicalServers'] + $levels['containers'] + $levels['certificates'] +
            $levels['sites'] + $levels['buildings'] + $levels['bays'] + $levels['physicalServers'] + $levels['physicalRouters'] + $levels['physicalSwitchs'] + $levels['physicalSecurityDevices']
            + $levels['wans'] + $levels['mans'] + $levels['lans'] + $levels['vlans'];

        $levels['maturity3'] =
            $denominator > 0 ?
            number_format(
                ($levels['entities_lvl1'] + $levels['relations_lvl2'] +
            $levels['macroProcessuses_lvl3'] + $levels['processes_lvl2'] + $levels['activities_lvl2'] + $levels['tasks_lvl3'] + $levels['operations_lvl2'] + $levels['actors_lvl2'] + $levels['informations_lvl2'] +
            $levels['applicationBlocks_lvl2'] + $levels['applications_lvl3'] + $levels['applicationServices_lvl2'] + $levels['applicationModules_lvl2'] + $levels['databases_lvl2'] + $levels['flows_lvl1'] +
            $levels['zones_ad_lvl1'] + $levels['annuaires_lvl1'] + $levels['forests_lvl1'] + $levels['domaines_lvl1'] +
            $levels['networks_lvl1'] + $levels['subnetworks_lvl1'] + $levels['gateways_lvl1'] + $levels['externalConnectedEntities_lvl2'] + $levels['switches_lvl1'] + $levels['routers_lvl1'] + $levels['securityDevices_lvl1'] + $levels['DHCPServers_lvl2'] + $levels['DNSServers_lvl2'] + $levels['clusters_lvl1'] + $levels['logicalServers_lvl1'] + $levels['containers_lvl1'] + $levels['certificates_lvl2'] +
            $levels['sites_lvl1'] + $levels['buildings_lvl1'] + $levels['bays_lvl1'] + $levels['physicalServers_lvl1'] + $levels['physicalRouters_lvl1'] + $levels['physicalSwitchs_lvl1'] + $levels['physicalSecurityDevices_lvl1'] + $levels['wans_lvl1'] + $levels['mans_lvl1'] + $levels['lans_lvl1'] + $levels['vlans_lvl1']) * 100 / $denominator,
                0
            ) : 0;

        return $levels;
    }
}
