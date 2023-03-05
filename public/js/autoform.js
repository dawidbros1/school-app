window.onload = () => {
   const type = document.getElementById('type').innerHTML;
   const form = document.getElementsByClassName('form')[0];
   const content = document.getElementById('content');

   const create = document.getElementById('create-handle');
   const edit = document.getElementById('edit-handle');
   const cancel = document.getElementById('cancel-handle');

   const selectedHandle = type == "create" ? create : edit;

   selectedHandle.addEventListener('click', () => {
      form.classList.toggle("d-none");
      content.classList.toggle("blur");
   })

   cancel.addEventListener('click', () => {
      form.classList.toggle("d-none");
      content.classList.toggle("blur");
   })

   type == "edit" ? edit.click() : null;
}