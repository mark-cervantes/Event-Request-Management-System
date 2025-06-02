// API configuration utility
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api/endpoints'

export const apiConfig = {
  baseURL: API_BASE_URL,
  
  // API endpoint builders
  endpoints: {
    
    // Reservation endpoints
    reservations: {
      read: () => `${API_BASE_URL}/readReservation.php`,
      readUpcoming: () => `${API_BASE_URL}/read_upcoming_reservations.php`,
      readSingle: () => `${API_BASE_URL}/read_singleReservation.php`,
      create: () => `${API_BASE_URL}/create_singleReservation.php`,
      update: () => `${API_BASE_URL}/update_reservation.php`,
      delete: () => `${API_BASE_URL}/delete_reservation.php`,
      search: () => `${API_BASE_URL}/search_reservations.php`,
      byStatus: () => `${API_BASE_URL}/read_reservations_by_status.php`,
      byFacility: () => `${API_BASE_URL}/read_reservations_by_facility.php`
    }
  }
}

export default apiConfig
