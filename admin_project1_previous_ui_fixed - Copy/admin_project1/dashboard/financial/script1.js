const pageFiles={"dashboard": "../dashboard/dashboard.php", "room-types": "../roomtype/roomtype.php", "rooms": "../room/room.php", "bookings": "../booking/booking.php", "reviews": "../review/review.php", "financial": "financial.php"};
function showPage(pageId){if(pageFiles[pageId])window.location.href=pageFiles[pageId];}
