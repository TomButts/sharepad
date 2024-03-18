<style scoped>
  .share-icon {
    float: right;
  }
</style>

<template>
  <div class="notes__sidebar">
    <button class="notes__add" type="button" v-on:click="newNote">
      Add Note
    </button>

    <div class="notes__list">
      <template v-for="(note, index) in notes">
        <div
          class="notes__list-item"
          v-bind:key="note.id"
          :class="{ 'notes__list-item--selected': index === 0 }"
          v-on:click="selectNote(note.id)"
        >
          <div class="notes__small-title">{{ title(note.body) }}
            <!-- todo: share icon -->
            <span class="share-icon share-button" @click.stop="handleShare(note.id)">
              +++
            </span>
          </div>
          <div class="notes__small-body">{{ preview(note.body) }}</div>
          <div class="notes__small-updated">{{ note.updated_at }}</div>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
  export default {
    props: ["notes"],
    methods: {
      selectNote: function (id) {
        this.$emit("select-note", id);
      },
      handleShare: function (id) {
        this.$emit("handle-share", id);
      },
      newNote: function () {
        this.$emit("new-note");
      },
      title: function (body) {
        if (0 === body.length) {
          return "New Note";
        }

        if (body.includes("\n")) {
          return body.split("\n")[0].substring(0, 100);
        }

        return body.substring(0, 100);
      },
      preview: function (body) {
        if (!body.includes("\n")) {
          return "No additional text";
        }

        let lines = body.split("\n").filter((line) => "" !== line);

        if (undefined !== lines[1]) {
          return lines[1].substring(0, 100);
        }

        return "No additional text";
      },
    },
  };
</script>
