// Minimal JS: theme toggle, CSRF token helper, simple form validation
document.addEventListener('DOMContentLoaded', ()=>{
  const btn = document.getElementById('theme-toggle');
  const saved = localStorage.getItem('theme') || 'light';
  if(saved==='dark') document.body.classList.add('dark');
  btn && btn.addEventListener('click', ()=>{
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', document.body.classList.contains('dark')?'dark':'light');
  });
  // Simple client-side validation for forms with data-validate
  document.querySelectorAll('form[data-validate]').forEach(form=>{
    form.addEventListener('submit', e=>{
      let valid = true; form.querySelectorAll('[required]').forEach(inp=>{ if(!inp.value.trim()){ valid=false; inp.classList.add('invalid'); }});
      if(!valid){ e.preventDefault(); alert('Please fill required fields.'); }
    });
  });
});

// small helper to toggle body class
function setTheme(t){ if(t==='dark') document.body.classList.add('dark'); else document.body.classList.remove('dark'); localStorage.setItem('theme',t); }

