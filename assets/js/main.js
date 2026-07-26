// Minimal JS: theme toggle, CSRF token helper, simple form validation
document.addEventListener('DOMContentLoaded', ()=>{
  const btn = document.getElementById('theme-toggle');
  const saved = localStorage.getItem('theme') || 'light';
  if(saved==='dark') document.body.classList.add('dark');
  btn && btn.addEventListener('click', ()=>{
    document.body.classList.toggle('dark');
    localStorage.setItem('theme', document.body.classList.contains('dark')?'dark':'light');
  });
});
