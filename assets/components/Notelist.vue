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
                    <div class="notes__small-title">{{ title(note.body) }}</div>
                    <div class="notes__small-body">{{ subHeading(note.body) }}</div>
                    <div class="notes__small-updated">{{ note.updated_at }}</div>
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
        },
        title: function (body) {
            return body.split('\n')[0].substring(0,100)
        },
        subHeading: function (body) {
            let lines = body.split('\n').filter(line => '' !== line)
            
            if (lines.length < 1) {
                return 'No additional text'
            }

            return lines[1].substring(0, 100);
        }
    }
}
</script>