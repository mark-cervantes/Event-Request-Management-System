#!/bin/bash

# Test script for Guest Reservation API
# Make sure your server is running before executing this script

BASE_URL="http://localhost:8080/sta_cruz_prj"  # Adjust this URL based on your setup

echo "Testing Guest Reservation API..."
echo "================================"

# Test 1: Valid request
echo "Test 1: Valid reservation request"
echo "-----------------------------------"
curl -X POST \
  "${BASE_URL}/api/endpoints/reservations/create_guest_reservation.php" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "Community Meeting",
    "facility": "Barangay Hall",
    "start_datetime": "2025-06-10 14:00:00",
    "end_datetime": "2025-06-10 16:00:00",
    "requested_by": "John Doe",
    "contact_number": "09123456789",
    "purpose": "Community planning session",
    "expected_attendees": 25
  }'
echo -e "\n\n"

# Test 2: Missing required field
echo "Test 2: Missing required field (event_name)"
echo "--------------------------------------------"
curl -X POST \
  "${BASE_URL}/api/endpoints/reservations/create_guest_reservation.php" \
  -H "Content-Type: application/json" \
  -d '{
    "facility": "Barangay Hall",
    "start_datetime": "2025-06-10 14:00:00",
    "end_datetime": "2025-06-10 16:00:00",
    "requested_by": "John Doe",
    "contact_number": "09123456789"
  }'
echo -e "\n\n"

# Test 3: Empty required field
echo "Test 3: Empty required field (contact_number)"
echo "----------------------------------------------"
curl -X POST \
  "${BASE_URL}/api/endpoints/reservations/create_guest_reservation.php" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "Community Meeting",
    "facility": "Barangay Hall",
    "start_datetime": "2025-06-10 14:00:00",
    "end_datetime": "2025-06-10 16:00:00",
    "requested_by": "John Doe",
    "contact_number": ""
  }'
echo -e "\n\n"

# Test 4: Invalid JSON
echo "Test 4: Invalid JSON format"
echo "----------------------------"
curl -X POST \
  "${BASE_URL}/api/endpoints/reservations/create_guest_reservation.php" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "Community Meeting",
    "facility": "Barangay Hall",
    "start_datetime": "2025-06-10 14:00:00",
    "end_datetime": "2025-06-10 16:00:00",
    "requested_by": "John Doe",
    "contact_number": "09123456789"
    // Missing closing brace to make it invalid JSON
  '
echo -e "\n\n"

# Test 5: Debug API
echo "Test 5: Testing debug API"
echo "-------------------------"
curl -X POST \
  "${BASE_URL}/api/endpoints/reservations/debug_guest_reservation.php" \
  -H "Content-Type: application/json" \
  -d '{
    "event_name": "Debug Test Meeting",
    "facility": "Meeting Room 1",
    "start_datetime": "2025-06-11 10:00:00",
    "end_datetime": "2025-06-11 12:00:00",
    "requested_by": "Debug User",
    "contact_number": "09987654321"
  }'
echo -e "\n\n"

echo "Testing complete!"
