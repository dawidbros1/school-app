window.onload = () => {
   var handles = document.getElementsByClassName("copy-wrapper")

   for (let i = 0; i < handles.length; i++) {
      let handle = handles[i];

      handle.addEventListener('click', () => {
         link = handle.dataset.link;
         navigator.clipboard.writeText(link);
      })
   }
}