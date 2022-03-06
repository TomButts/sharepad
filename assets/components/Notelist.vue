<template>
    <div class="notes__sidebar">
        <button class="notes__add"
            type="button"
            v-on:click="newNote"
        >
            Add Note
        </button>

        <div class="notes__list">
            <template v-for="(note, index) in notes">
                <div class="notes__list-item"
                    v-bind:key="note.id"
                    :class="{ 'notes__list-item--selected': index === 0 }"
                    v-on:click="selectNote(note.id)"
                >
                    <div class="notes__small-title">{{ note.title }}</div>
                    <div class="notes__small-body">{{ note.body }}</div>
                    <div class="notes__small-updated">{{ note.date }}</div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {    
    props: ['notes'],
    methods: {
        selectNote: function (id) {
            this.$emit('select-note', id)
        },
        newNote: function () {
            this.$emit('new-note')
        }
    }
}
</script>