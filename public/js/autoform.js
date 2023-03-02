window.onload = () => {
   var form = document.getElementsByClassName('form')[0];
   var handle = document.getElementsByClassName("handle");
   var content = document.getElementById('content');

   for (let i = 0; i < handle.length; i++) {
      handle[i].addEventListener('click', () => {
         form.classList.toggle("d-none");
         content.classList.toggle("blur");
      })
   }

   const type = document.getElementById('type').innerHTML;

   if (type == "edit") {
      handle[0].click();
   }
}