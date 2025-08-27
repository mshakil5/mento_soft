<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
  ClassicEditor.create(document.querySelector('#new-task-description'))
    .catch(error => console.error(error));

  document.querySelectorAll('[id^="description-"]').forEach((el) => {
      ClassicEditor.create(el).catch(error => console.error(error));
  });
</script>