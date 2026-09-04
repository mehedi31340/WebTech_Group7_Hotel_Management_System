const pageFiles = {
  dashboard: 'dashboard.php',
  'room-types': 'roomtype.php',
  rooms: 'room.php',
  bookings: 'booking.php',
  reviews: 'review.php',
  financial: 'financial.php'
};

function showPage(pageId) {
  const file = pageFiles[pageId];
  if (file) window.location.href = file;
}

function filterBookings() {
  const status = document.getElementById('status')?.value || 'all';
  const roomType = document.getElementById('roomType')?.value || 'all';
  const source = document.getElementById('source')?.value || 'all';
  document.querySelectorAll('#bookingTableBody tr').forEach(row => {
    const okStatus = status === 'all' || row.dataset.status === status;
    const okRoom = roomType === 'all' || row.dataset.room === roomType;
    const okSource = source === 'all' || row.dataset.source === source;
    row.style.display = okStatus && okRoom && okSource ? '' : 'none';
  });
}

document.addEventListener('DOMContentLoaded', () => {
  filterBookings();
});
