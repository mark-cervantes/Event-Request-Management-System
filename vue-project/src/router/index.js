import { createRouter, createWebHistory } from 'vue-router'

// Views
import Login from '@/view/Login.vue'
import AdminDashboard from '@/view/AdminDashboard.vue'
import AdminEventsPage from '@/view/AdminEventsPage.vue'
import UserHomepage from '@/view/UserHomepage.vue'
import UserManagement from '@/view/UserManagement.vue'
import ResidentManagement from '@/view/ResidentManagement.vue'
import GuestHomepage from '@/view/GuestHomepage.vue'
import RecordComplaint from '@/view/RecordComplaint.vue'
import ComplaintForm from '@/view/ComplaintForm.vue'
import EventsTable from '@/view/EventsTable.vue'
import GuestEventsPage from '@/view/user/UserEventsPage.vue'
import BusinessClearance from '@/view/BusinessClearance.vue'
// Layouts
import AdminLayout from '@/layout/AdminLayout.vue'
import UserLayout from '@/layout/UserLayout.vue'
import GuestLayout from '@/layout/GuestLayout.vue'

const routes = [
  { path: '/', name: 'Login', component: Login },


  // Layout-based admin routes
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      { path: 'dashboard', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'user-management', name: 'UserManagement', component: UserManagement },
      { path: 'resident-management', name: 'ResidentManagement', component: ResidentManagement },
      { path: 'record-complaint', name: 'RecordComplaint', component: RecordComplaint },
      { path: 'events-table', name: 'EventsTable', component: EventsTable },
      { path: 'business-clearance', name: 'BusinessClearance', component: BusinessClearance },
      { path: 'event-scheduling', name: 'AdminEventsPage', component: AdminEventsPage }
    ]
  },


  // Layout-based user routes
  {
    path: '/user',
    component: UserLayout,
    children: [
      { path: 'user-homepage', name: 'UserHomepage', component: UserHomepage },
      { path: 'complaint-form', name: 'ComplaintForm', component: ComplaintForm },
      { path: 'event-scheduling', name: 'UserEventsForm', component: GuestEventsPage }
    ]
  },

  {
    path: '/guest',
    component: GuestLayout,
    children: [
      { path: 'guest-homepage', name: 'GuestHomepage', component: GuestHomepage },
      { path: 'events', name: 'GuestEventsPage', component: GuestEventsPage }
    ]
  },

  { path: '/AdminDashboard', redirect: '/admin/dashboard' },
  { path: '/UserHomepage', redirect: '/user/user-homepage' },
  { path: '/GuestHomepage', redirect: '/guest/guest-homepage' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
