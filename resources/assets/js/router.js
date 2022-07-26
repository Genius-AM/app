import VueRouter from 'vue-router'
import DriverGeneral from './views/djipping/driver/General'
import DriverRoute from './views/djipping/driver/Route'
import ManagerGeneral from './views/djipping/manager/General'
import ManagerGeneral2 from './views/djipping/manager/General2'
import ManagerAddress from './views/djipping/manager/Address'
import ManagerRouteTime from './views/djipping/manager/RouteTime'
import ManagerDeletedOrders from './views/djipping/manager/DeletedOrders'
import ManagerOrders from './views/djipping/manager/Orders'
import QuadroDriverGeneral from './views/quadro/driver/General'
import QuadroManagerGeneral from './views/quadro/manager/General'
import QuadroManagerDeletedOrders from './views/quadro/manager/DeletedOrders'
import QuadroManagerOrders from './views/quadro/manager/Orders'
import DivingManagerGeneral from './views/diving/manager/General'
import DivingManagerDeletedOrders from './views/diving/manager/DeletedOrders'
import DivingManagerOrders from './views/diving/manager/Orders'
import SeaManagerGeneral from './views/sea/manager/General'
import SeaManagerDeletedOrders from './views/sea/manager/DeletedOrders'
import SeaManagerOrders from './views/sea/manager/Orders'
import SeaAdultsChildren from './views/sea/manager/AdultsChildren'

const routes = [
    {
        path: '/reports/show/djipping/driver/general',
        name: 'djipping.driver.general',
        component: DriverGeneral
    },
    {
        path: '/reports/show/djipping/driver/route',
        name: 'djipping.driver.route',
        component: DriverRoute
    },
    {
        path: '/reports/show/djipping/manager/general',
        name: 'djipping.manager.general',
        component: ManagerGeneral
    },
    {
        path: '/reports/show/djipping/manager/general2',
        name: 'djipping.manager.general2',
        component: ManagerGeneral2
    },
    {
        path: '/reports/show/djipping/manager/address',
        name: 'djipping.manager.address',
        component: ManagerAddress
    },
    {
        path: '/reports/show/djipping/manager/route-time',
        name: 'djipping.manager.routeTime',
        component: ManagerRouteTime
    },
    {
        path: '/reports/show/djipping/manager/deleted-orders',
        name: 'djipping.manager.deletedOrders',
        component: ManagerDeletedOrders
    },
    {
        path: '/reports/show/djipping/manager/orders',
        name: 'djipping.manager.orders',
        component: ManagerOrders
    },
    {
        path: '/reports/show/quadro/driver/general',
        name: 'quadro.driver.general',
        component: QuadroDriverGeneral
    },
    {
        path: '/reports/show/quadro/manager/general',
        name: 'quadro.manager.general',
        component: QuadroManagerGeneral
    },
    {
        path: '/reports/show/quadro/manager/deleted-orders',
        name: 'quadro.manager.deletedOrders',
        component: QuadroManagerDeletedOrders
    },
    {
        path: '/reports/show/quadro/manager/orders',
        name: 'quadro.manager.orders',
        component: QuadroManagerOrders
    },
    {
        path: '/reports/show/diving/manager/general',
        name: 'diving.manager.general',
        component: DivingManagerGeneral
    },
    {
        path: '/reports/show/diving/manager/deleted-orders',
        name: 'diving.manager.deletedOrders',
        component: DivingManagerDeletedOrders
    },
    {
        path: '/reports/show/diving/manager/orders',
        name: 'diving.manager.orders',
        component: DivingManagerOrders
    },
    {
        path: '/reports/show/sea/manager/general',
        name: 'sea.manager.general',
        component: SeaManagerGeneral
    },
    {
        path: '/reports/show/sea/manager/deleted-orders',
        name: 'sea.manager.deletedOrders',
        component: SeaManagerDeletedOrders
    },
    {
        path: '/reports/show/sea/manager/orders',
        name: 'sea.manager.orders',
        component: SeaManagerOrders
    },
    {
        path: '/reports/show/sea/manager/adults-children',
        name: 'sea.manager.adultsChildren',
        component: SeaAdultsChildren
    },
];

const router = new VueRouter({
    mode: 'history',
    routes
});

export default router
