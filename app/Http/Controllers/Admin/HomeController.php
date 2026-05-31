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
use App\Models\Cartographer;

class HomeController extends Controller
{
    private function q(string $class): \Illuminate\Database\Eloquent\Builder
    {
        return Cartographer::scopedQuery($class);
    }

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
            'data_processing'   => $this->q(DataProcessing::class)->count(),
            'security_controls' => $this->q(SecurityControl::class)->count(),

            // Ecosystem
            'entities'      => $this->q(Entity::class)->count(),
            'entities_lvl1' => $this->q(Entity::class)->maturityLevel1()->count(),

            'relations'      => $this->q(Relation::class)->count(),
            'relations_lvl1' => $this->q(Relation::class)->maturityLevel1()->count(),
            'relations_lvl2' => $this->q(Relation::class)->maturityLevel2()->count(),

            // Information system
            'macroProcessuses'      => $this->q(MacroProcessus::class)->count(),
            'macroProcessuses_lvl2' => $this->q(MacroProcessus::class)->maturityLevel2()->count(),
            'macroProcessuses_lvl3' => $this->q(MacroProcessus::class)->maturityLevel3()->count(),

            'processes'      => $this->q(Process::class)->count(),
            'processes_lvl1' => $this->q(Process::class)->maturityLevel1()->count(),
            'processes_lvl2' => $this->q(Process::class)->maturityLevel2()->count(),

            'activities'      => $this->q(Activity::class)->count(),
            'activities_lvl2' => $this->q(Activity::class)->maturityLevel2()->count(),

            'operations'      => $this->q(Operation::class)->count(),
            'operations_lvl1' => $this->q(Operation::class)->maturityLevel1()->count(),
            'operations_lvl2' => $this->q(Operation::class)->maturityLevel2()->count(),
            'operations_lvl3' => $this->q(Operation::class)->maturityLevel3()->count(),

            'tasks'      => $this->q(Task::class)->count(),
            'tasks_lvl3' => $this->q(Task::class)->maturityLevel3()->count(),

            'actors'      => $this->q(Actor::class)->count(),
            'actors_lvl2' => $this->q(Actor::class)->maturityLevel2()->count(),

            'informations'      => $this->q(Information::class)->count(),
            'informations_lvl1' => $this->q(Information::class)->maturityLevel1()->count(),
            'informations_lvl2' => $this->q(Information::class)->maturityLevel2()->count(),

            // Applications
            'applicationBlocks'      => $this->q(ApplicationBlock::class)->count(),
            'applicationBlocks_lvl2' => $this->q(ApplicationBlock::class)->maturityLevel2()->count(),

            'applications'      => $this->q(Application::class)->count(),
            'applications_lvl1' => $this->q(Application::class)->maturityLevel1()->count(),
            'applications_lvl2' => $this->q(Application::class)->maturityLevel2()->count(),
            'applications_lvl3' => $this->q(Application::class)->maturityLevel3()->count(),

            'applicationServices'      => $this->q(ApplicationService::class)->count(),
            'applicationServices_lvl2' => $this->q(ApplicationService::class)->maturityLevel2()->count(),

            'applicationModules'      => $this->q(ApplicationModule::class)->count(),
            'applicationModules_lvl2' => $this->q(ApplicationModule::class)->maturityLevel2()->count(),

            'databases'      => $this->q(Database::class)->count(),
            'databases_lvl1' => $this->q(Database::class)->maturityLevel1()->count(),
            'databases_lvl2' => $this->q(Database::class)->maturityLevel2()->count(),

            'flows'      => $this->q(ApplicationFlow::class)->count(),
            'flows_lvl1' => $this->q(ApplicationFlow::class)->maturityLevel1()->count(),

            // Administration
            'zones_ad'      => $this->q(ZoneAdmin::class)->count(),
            'zones_ad_lvl1' => $this->q(ZoneAdmin::class)->maturityLevel1()->count(),

            'annuaires'      => $this->q(Annuaire::class)->count(),
            'annuaires_lvl1' => $this->q(Annuaire::class)->maturityLevel1()->count(),

            'forests'      => $this->q(ForestAd::class)->count(),
            'forests_lvl1' => $this->q(ForestAd::class)->maturityLevel1()->count(),

            'domains'       => $this->q(Domain::class)->count(),
            'domaines_lvl1' => $this->q(Domain::class)->maturityLevel1()->count(),

            // Logique
            'networks'      => $this->q(Network::class)->count(),
            'networks_lvl1' => $this->q(Network::class)->maturityLevel1()->count(),

            'subnetworks'      => $this->q(Subnetwork::class)->count(),
            'subnetworks_lvl1' => $this->q(Subnetwork::class)->maturityLevel1()->count(),

            'gateways'      => $this->q(Gateway::class)->count(),
            'gateways_lvl1' => $this->q(Gateway::class)->maturityLevel1()->count(),

            'externalConnectedEntities'      => $this->q(ExternalConnectedEntity::class)->count(),
            'externalConnectedEntities_lvl2' => $this->q(ExternalConnectedEntity::class)->maturityLevel2()->count(),

            'switches'      => $this->q(NetworkSwitch::class)->count(),
            'switches_lvl1' => $this->q(NetworkSwitch::class)->maturityLevel1()->count(),

            'routers'      => $this->q(Router::class)->count(),
            'routers_lvl1' => $this->q(Router::class)->maturityLevel1()->count(),

            'securityDevices'      => $this->q(SecurityDevice::class)->count(),
            'securityDevices_lvl1' => $this->q(SecurityDevice::class)->maturityLevel1()->count(),

            'DHCPServers'      => $this->q(DhcpServer::class)->count(),
            'DHCPServers_lvl2' => $this->q(DhcpServer::class)->maturityLevel2()->count(),

            'DNSServers'      => $this->q(Dnsserver::class)->count(),
            'DNSServers_lvl2' => $this->q(Dnsserver::class)->maturityLevel2()->count(),

            'clusters'      => $this->q(Cluster::class)->count(),
            'clusters_lvl1' => $this->q(Cluster::class)->maturityLevel1()->count(),

            'logicalServers'      => $this->q(LogicalServer::class)->count(),
            'logicalServers_lvl1' => $this->q(LogicalServer::class)->maturityLevel1()->count(),

            'containers'      => $this->q(Container::class)->count(),
            'containers_lvl1' => $this->q(Container::class)->maturityLevel1()->count(),

            'certificates'      => $this->q(Certificate::class)->count(),
            'certificates_lvl2' => $this->q(Certificate::class)->maturityLevel2()->count(),

            // Physical
            'sites'      => $this->q(Site::class)->count(),
            'sites_lvl1' => $this->q(Site::class)->maturityLevel1()->count(),

            'buildings'      => $this->q(Building::class)->count(),
            'buildings_lvl1' => $this->q(Building::class)->maturityLevel1()->count(),

            'bays'      => $this->q(Bay::class)->count(),
            'bays_lvl1' => $this->q(Bay::class)->maturityLevel1()->count(),

            'zones' => $this->q(Zone::class)->count(),

            'physicalServers'      => $this->q(PhysicalServer::class)->count(),
            'physicalServers_lvl1' => $this->q(PhysicalServer::class)->maturityLevel1()->count(),

            'workstations'      => $this->q(Workstation::class)->count(),
            'workstations_lvl1' => $this->q(Workstation::class)->maturityLevel1()->count(),

            'storageDevices'      => $this->q(StorageDevice::class)->count(),
            'storageDevices_lvl1' => $this->q(StorageDevice::class)->maturityLevel1()->count(),

            'peripherals'      => $this->q(Peripheral::class)->count(),
            'peripherals_lvl1' => $this->q(Peripheral::class)->maturityLevel1()->count(),

            'phones'      => $this->q(Phone::class)->count(),
            'phones_lvl1' => $this->q(Phone::class)->maturityLevel1()->count(),

            'physicalSwitchs'      => $this->q(PhysicalSwitch::class)->count(),
            'physicalSwitchs_lvl1' => $this->q(PhysicalSwitch::class)->maturityLevel1()->count(),

            'physicalRouters'      => $this->q(PhysicalRouter::class)->count(),
            'physicalRouters_lvl1' => $this->q(PhysicalRouter::class)->maturityLevel1()->count(),

            'wifiTerminals'      => $this->q(WifiTerminal::class)->count(),
            'wifiTerminals_lvl1' => $this->q(WifiTerminal::class)->maturityLevel1()->count(),

            'physicalSecurityDevices'      => $this->q(PhysicalSecurityDevice::class)->count(),
            'physicalSecurityDevices_lvl1' => $this->q(PhysicalSecurityDevice::class)->maturityLevel1()->count(),

            'wans'      => ($wan_count = $this->q(Wan::class)->count()),
            'wans_lvl1' => $wan_count,

            'mans'      => ($man_count = $this->q(Man::class)->count()),
            'mans_lvl1' => $man_count,

            'lans'      => $this->q(Lan::class)->count(),
            'lans_lvl1' => $this->q(Lan::class)->maturityLevel1()->count(),

            'vlans'      => $this->q(Vlan::class)->count(),
            'vlans_lvl1' => $this->q(Vlan::class)->maturityLevel1()->count(),
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
