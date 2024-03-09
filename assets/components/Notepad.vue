<template>
  <div class="notes__preview">
    <textarea
      v-model="note.body"
      class="notes__body"
      placeholder="Write a beautiful note here..."
    ></textarea>
  </div>
</template>

<script>
  export default {
    props: ["note"],
  };
</script>
