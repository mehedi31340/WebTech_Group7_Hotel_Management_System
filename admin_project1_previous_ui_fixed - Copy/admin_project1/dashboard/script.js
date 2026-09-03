const pageFiles={"dashboard":"dashboard.php","room-types":"../roomtype/roomtype.php","rooms":"../room/room.php","bookings":"../booking/booking.php","reviews":"../review/review.php","financial":"../financial/financial.php"};
function showPage(pageId){if(pageFiles[pageId])window.location.href=pageFiles[pageId];}
const floorMap=document.getElementById("floormap");
if(floorMap){const rooms=[[101,"available"],[102,"maintenance"],[103,"occupied"],[104,"available"],[105,"blocked"],[106,"occupied"],[107,"occupied"],[108,"available"],[109,"occupied"],[110,"occupied"],[111,"occupied"],[112,"available"]];floorMap.innerHTML=rooms.map(([number,status])=>`<div class="room ${status}">${number}</div>`).join("");}
