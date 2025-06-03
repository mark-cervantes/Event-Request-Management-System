<template>
  <Doughnut :data="chartData" :options="chartOptions" />
</template>

<script setup>
  import { computed, onMounted, ref } from 'vue'
  import { Doughnut } from 'vue-chartjs'
  import { ArcElement, Chart, Legend, Tooltip } from 'chart.js'
import { buildApiUrl } from '@/utils/api'

  Chart.register(ArcElement, Tooltip, Legend)

  // Reactive array to hold complaints fetched from API
  const complaints = ref([])

  // Fetch complaints from your API endpoint
  async function fetchComplaints () {
    try {
      const res = await fetch(buildApiUrl('/complaints/read_complaints.php'))
      const data = await res.json()
      complaints.value = data.data || []
    } catch (error) {
      console.error('Failed to fetch complaints:', error)
    }
  }

  onMounted(() => {
    fetchComplaints()
    setInterval(fetchComplaints, 2000)
  })

  // Helper to normalize status strings
  function normalizeStatus (status) {
    return status?.trim().toLowerCase()
  }

  // Computed counts for each status
  const totalComplaints = computed(() => complaints.value.length)
  const resolvedCount = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'resolved').length
  )
  const inProgressCount = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'in progress').length
  )
  const pendingCount = computed(() =>
    complaints.value.filter(c => normalizeStatus(c.status) === 'pending').length
  )

  // Reactive chart data that updates when counts change
  const chartData = computed(() => ({
    labels: ['Total Complaints', 'Resolved', 'In Progress', 'Pending'],
    datasets: [
      {
        data: [totalComplaints.value, resolvedCount.value, inProgressCount.value, pendingCount.value],
        backgroundColor: ['#22d3ee', '#29d657', '#facc15', '#ef4444'],
        borderWidth: 0.5,
        borderColor: '#18191a',
        hoverOffset: 8,
      },
    ],
  }))

  const chartOptions = {
    cutout: '65%',
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true },
    },
    responsive: true,
    maintainAspectRatio: false,
  }
</script>
