import './styles/app.css';
import './bootstrap';
import Vue from 'vue';
import Notepad from './components/Notepad.vue';
import Notelist from './components/Notelist.vue';

new Vue({
    el: '#app',
    components: {Notepad, Notelist},
    data() {
        return {
            note: {
                id: 1,
                title: 'Title 1',
                body: 'This is a character limited version of what is in the note',
                date: '2022-03-03 12:00:00'
            },
            notes: [
                {
                    id: 1,
                    title: 'Title 1',
                    body: 'This is a character limited version of what is in the note',
                    date: '2022-03-03 12:00:00'
                },
                {
                    id: 2,
                    title: 'Title 2',
                    body: 'This is not a drill',
                    date: '2022-03-03 12:00:00'
                },
                {
                    id: 3,
                    title: 'Title 3',
                    body: 'This could be a drill?!',
                    date: '2022-03-03 12:00:00'
                }
            ]
        }
    },
    methods: {
        changeNote: function (id) {
            const selectedNoteIndex = this.notes.findIndex((note) => {
                return note.id === id
            })

            // todo save final state of previous active note

            let selectedNote = this.notes[selectedNoteIndex]

            // move element to front of array
            this.notes.splice(selectedNoteIndex, 1)
            this.notes.unshift(selectedNote)

            // select the note
            this.note = selectedNote
        },
        newNote: function () {
            // todo: save previous active note

            const newNote = {
                title: 'New note',
                body: '',
                date: '2022-12-12'
            }

            // todo persist the dummy data and send back an id
            newNote.id = 6666

            this.notes.unshift(newNote)

            this.note = newNote
        },
        saveNote: function (note) {
            // todo
        }
    }
})
