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

// Calendly – lädt erst nach Klick (DSGVO-konform)
(function(){
  const btn=document.getElementById('calendly-load-btn');
  if(!btn)return;
  let loaded=false;
  btn.addEventListener('click',function(){
    if(loaded)return;
    loaded=true;
    const consent=document.getElementById('calendly-consent');
    const embed=document.getElementById('calendly-embed');
    // Calendly-CSS + Script nachladen
    const link=document.createElement('link');
    link.rel='stylesheet';
    link.href='https://assets.calendly.com/assets/external/widget.css';
    document.head.appendChild(link);
    const script=document.createElement('script');
    script.src='https://assets.calendly.com/assets/external/widget.js';
    script.async=true;
    script.onload=function(){
      if(consent)consent.style.display='none';
      if(embed){
        embed.style.display='block';
        if(window.Calendly){
          window.Calendly.initInlineWidget({
            url:embed.getAttribute('data-url'),
            parentElement:embed
          });
        }
      }
    };
    document.body.appendChild(script);
  });
})();

// Smooth scroll für Anker-Links
document.querySelectorAll('a[href^="#"]').forEach(function(a){
  a.addEventListener('click',function(e){
    const id=this.getAttribute('href').slice(1);
    const el=document.getElementById(id);
    if(el){e.preventDefault();el.scrollIntoView({behavior:'smooth',block:'start'});}
  });
});
