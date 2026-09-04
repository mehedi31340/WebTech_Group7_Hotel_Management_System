document.addEventListener("DOMContentLoaded",()=>{
  const form=document.getElementById("searchForm");
  if(!form) return;
  const results=document.getElementById("roomResults"), notice=document.getElementById("seasonNotice");
  form.addEventListener("submit",async e=>{
    e.preventDefault();
    const p=new URLSearchParams(new FormData(form));
    results.innerHTML='<div class="loading">Searching available rooms…</div>';
    try{
      const r=await fetch("api/rooms.php?"+p.toString()); const data=await r.json();
      if(!data.ok){results.innerHTML='<div class="alert error">'+data.message+'</div>';return;}
      notice.innerHTML=data.seasonal_notice?`<div class="season"><b>${data.seasonal_notice.title}</b><span>${data.seasonal_notice.description} ${data.seasonal_notice.percentage}% promotional pricing.</span></div>`:"";
      if(!data.rooms.length){results.innerHTML='<div class="empty">No matching room types found.</div>';return;}
      results.innerHTML=data.rooms.map(x=>`
      <article class="room-card">
        <img src="${x.image}" alt="">
        <div class="room-info"><span class="badge available">${x.available} available</span><h3>${x.name}</h3>
        <p>${x.description}</p><div class="room-meta">👥 ${x.capacity} guests · ${x.nights} nights</div>
        <strong class="price">৳ ${x.price.toLocaleString('en-BD',{minimumFractionDigits:2})} <small>/ night</small></strong>
        <a class="btn full" href="index.php?page=room&id=${x.id}">View Details</a>
        <form method="post" action="index.php?page=book" class="quick-book">
          <input type="hidden" name="room_type_id" value="${x.id}"><input type="hidden" name="checkin" value="${p.get('checkin')}"><input type="hidden" name="checkout" value="${p.get('checkout')}"><input type="hidden" name="guests" value="${p.get('guests')}">
          <input name="special_requests" placeholder="Special request (optional)"><button class="btn ghost full">Book Now · ৳ ${x.total.toLocaleString('en-BD')}</button>
        </form></div></article>`).join("");
    }catch(err){results.innerHTML='<div class="alert error">Could not load rooms. Check the database/API.</div>'}
  });
});