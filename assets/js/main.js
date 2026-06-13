// Cookie Consent
(function(){
  const KEY='nsb_cookie_consent';
  const banner=document.getElementById('cookie-banner');
  if(!banner)return;
  if(localStorage.getItem(KEY)){banner.style.display='none';if(localStorage.getItem(KEY)==='all')loadMap();return;}
  document.getElementById('cookie-accept').addEventListener('click',function(){
    localStorage.setItem(KEY,'all');banner.style.display='none';loadMap();
  });
  document.getElementById('cookie-decline').addEventListener('click',function(){
    localStorage.setItem(KEY,'necessary');banner.style.display='none';
  });
})();

function loadMap(){
  const ph=document.getElementById('maps-placeholder');
  const frame=document.getElementById('maps-frame-wrap');
  if(ph)ph.style.display='none';
  if(frame)frame.style.display='block';
}

// Maps-Button im Placeholder
(function(){
  const btn=document.getElementById('maps-consent-btn');
  if(btn){
    btn.addEventListener('click',function(){
      localStorage.setItem('nsb_cookie_consent','all');
      loadMap();
      const banner=document.getElementById('cookie-banner');
      if(banner)banner.style.display='none';
    });
  }
})();

// Hamburger-Menü
function toggleMenu(){
  const links=document.getElementById('nav-links');
  const icon=document.getElementById('hamburger-icon');
  const open=links.classList.toggle('open');
  icon.innerHTML=open
    ?'<line x1="4" y1="4" x2="20" y2="20"/><line x1="20" y1="4" x2="4" y2="20"/>'
    :'<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>';
}
function closeMenu(){
  const links=document.getElementById('nav-links');
  const icon=document.getElementById('hamburger-icon');
  if(links)links.classList.remove('open');
  if(icon)icon.innerHTML='<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>';
}

// Einfacher Kalender-Widget
(function(){
  const grid=document.getElementById('cal-grid');
  if(!grid)return;
  const monthLabel=document.getElementById('cal-month');
  const months=['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'];
  let now=new Date();
  let year=now.getFullYear(),month=now.getMonth();
  const freeSlots=[3,7,10,14,17,21,24,28];

  function render(){
    if(monthLabel)monthLabel.textContent=months[month]+' '+year;
    const first=new Date(year,month,1).getDay();
    const offset=(first===0?6:first-1);
    const days=new Date(year,month+1,0).getDate();
    grid.innerHTML='';
    for(let i=0;i<offset;i++){
      const e=document.createElement('div');
      e.className='cal-day empty';
      grid.appendChild(e);
    }
    const todayIs=(year===now.getFullYear()&&month===now.getMonth())?now.getDate():-1;
    for(let d=1;d<=days;d++){
      const e=document.createElement('div');
      e.textContent=d;
      let cls='cal-day';
      if(d===todayIs)cls+=' today';
      else if(freeSlots.includes(d))cls+=' slot';
      e.className=cls;
      if(cls.includes('slot')){
        e.title='Termin verfügbar';
        e.addEventListener('click',function(){
          document.querySelectorAll('.cal-day').forEach(x=>x.classList.remove('booked'));
          e.classList.remove('slot');e.classList.add('booked');
        });
      }
      grid.appendChild(e);
    }
  }
  render();
  const prev=document.getElementById('cal-prev');
  const next=document.getElementById('cal-next');
  if(prev)prev.addEventListener('click',function(){month--;if(month<0){month=11;year--;}render();});
  if(next)next.addEventListener('click',function(){month++;if(month>11){month=0;year++;}render();});
})();

// Smooth scroll für Anker-Links
document.querySelectorAll('a[href^="#"]').forEach(function(a){
  a.addEventListener('click',function(e){
    const id=this.getAttribute('href').slice(1);
    const el=document.getElementById(id);
    if(el){e.preventDefault();el.scrollIntoView({behavior:'smooth',block:'start'});}
  });
});
