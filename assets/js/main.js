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

// Hero-Slider (JS-gesteuert, zuverlässig)
(function(){
  const slides=document.querySelectorAll('.hero-slide');
  if(slides.length<2)return;

  // Slide 1 laedt sofort (LCP); Slides 2-10 tragen nur data-bg und werden
  // erst nachgeladen, wenn der Hauptthread frei ist (schont Ladezeit/LCP).
  function lazyLoadSlides(){
    slides.forEach(function(s){
      var bg=s.getAttribute('data-bg');
      if(bg){ s.style.backgroundImage="url('"+bg+"')"; s.removeAttribute('data-bg'); }
    });
  }
  if('requestIdleCallback' in window){ requestIdleCallback(lazyLoadSlides,{timeout:1500}); }
  else { window.addEventListener('load', lazyLoadSlides); }

  let idx=0;
  slides[0].classList.add('is-active');
  setInterval(function(){
    slides[idx].classList.remove('is-active');
    idx=(idx+1)%slides.length;
    slides[idx].classList.add('is-active');
  },3000);
})();

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

/* Intensiv-Termine: nur noch kommende Termine sind anklickbar.
   Abgleich mit dem heutigen Datum bei jedem Seitenaufruf – vergangene
   Termine werden automatisch nicht mehr verlinkt (bleiben als Historie sichtbar). */
(function(){
  var liste = document.getElementById('intensiv-liste');
  if(!liste) return;
  var heute = new Date(); heute.setHours(0,0,0,0);
  var offen = 0;
  liste.querySelectorAll('li').forEach(function(li){
    var end = li.getAttribute('data-end');
    var key = li.getAttribute('data-key');
    var endDate = end ? new Date(end + 'T23:59:59') : null;
    if(endDate && !isNaN(endDate) && endDate >= heute && key){
      li.classList.add('int-offen');
      li.setAttribute('role','link');
      li.setAttribute('tabindex','0');
      li.setAttribute('aria-label', li.textContent.replace(/\s+/g,' ').trim() + ' – jetzt anmelden');
      var go = function(){ window.location.href = 'anmeldung.html?t=' + encodeURIComponent(key); };
      li.addEventListener('click', go);
      li.addEventListener('keydown', function(e){ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); go(); } });
      offen++;
    } else if(endDate && !isNaN(endDate) && endDate < heute){
      li.classList.add('int-vorbei');
    }
  });
  var hint = document.getElementById('intensiv-hint');
  if(hint && offen > 0){ hint.hidden = false; }
})();
