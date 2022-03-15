import './styles/app.scss';
import './bootstrap';
import Vue from 'vue';
import axios from 'axios';
import Notepad from './components/Notepad.vue';
import Notelist from './components/Notelist.vue';

new Vue({
    el: '#app',
    components: {Notepad, Notelist},
    data() {
        return {
            note: {},
            notes: []
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
    },
    mounted() {
        axios.get('/notes').then((response) => {
            this.notes = response.data.notes
            this.note = response.data.notes[0]
        });
    }
})
