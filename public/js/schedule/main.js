window.onload = () => {
   var wrappers = document.getElementsByClassName('schedule-wrapper');
   var handles = document.getElementsByClassName('handle');

   for (let i = 0; i < handles.length; i++) {
      handles[i].addEventListener('click', () => {
         reset();
         handles[i].classList.add('active')
         wrappers[i].classList.remove('d-none')
      })
   }

   function reset() {
      for (let i = 0; i < wrappers.length; i++) {
         wrappers[i].classList.add('d-none');
         handles[i].classList.remove('active')
      }
   }

   handles[0].click();
}