<template>
  <div class="notes-preview">
    <textarea
      v-model="note.body"
      class="notes-body"
      placeholder="Write a beautiful note here..."
    ></textarea>
  </div>
</template>

<script>
  export default {
    props: ["note"],
  };
</script>
