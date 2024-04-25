import './styles/app.scss'
import Vue from 'vue'
import axios from 'axios'
import Notepad from './components/Notepad.vue'
import Notelist from './components/Notelist.vue'
import Share from './components/Share.vue'
// import { debounce } from 'debounce'
import moment from 'moment'

const blankNote = {
  id: 0,
  body: '',
  created_at: moment().format('DD-MM-YY HH:mm:ss'),
  updated_at: moment().format('DD-MM-YY HH:mm:ss')
}

new Vue({ // eslint-disable-line
  el: '#app',
  components: { Notepad, Notelist, Share },
  data () {
    return {
      note: blankNote,
      notes: [blankNote],
      activeShareNote: blankNote,
      showShareModal: false
    }
  },
  methods: {
    changeNote: function (id) {
      const selectedNoteIndex = this.notes.findIndex(note => {
        return note.id === id
      })

      // todo: save final state of previous active note
      const selectedNote = this.notes[selectedNoteIndex]

      this.notes.splice(selectedNoteIndex, 1)

      this.popNewNote(selectedNote)
    },
    handleShare: function (sharedNoteId) {
      const noteToShare = this.notes.find(note => note.id === sharedNoteId)

      if (!noteToShare) {
        // todo: handle error
      }

      this.activeShareNote = noteToShare

      this.showShareModal = true
    },
    closeShareModal: function () {
      this.showShareModal = false
    },
    popNewNote: function (note) {
      this.notes.unshift(note)
      this.note = note
    },
    newNote: function () {
      if (this.note.body.length > 0) {
        axios
          .post('/note/save', {
            id: this.note.id,
            body: this.note.body
          })
          .then(response => {
            this.popNewNote(blankNote)
          })
      }
    }
  },
  watch: {
    note: {
      handler: function (e) {
        // todo: re add back in debounce and prevent infinite loop
        // axios
        //   .post('/note/save', {
        //     id: this.note.id,
        //     body: this.note.body
        //   })
        //   .then(response => {
        //     if (this.note.id === 0 && response.data.note !== 0) {
        //       this.note.id = response.data.note.id
        //     }
        //   })
      },
      deep: true
    }
  },
  mounted () {
    axios
      .get('/notes')
      .then(response => {
        if (response.data.notes.length !== 0) {
          this.notes = response.data.notes

          if (response.data.notes && response.data.notes.length > 0) {
            this.note = response.data.notes[0]
          }
        }
      })
      .then(response => {
        const eventSource = new EventSource( // eslint-disable-line
          JSON.parse(document.getElementById('mercure-url').textContent),
          { withCredentials: true }
        )

        eventSource.onmessage = event => {
          const eventData = JSON.parse(event.data)

          if (
            Object.prototype.hasOwnProperty.call(eventData, 'id') &&
            Object.prototype.hasOwnProperty.call(eventData, 'body')
          ) {
            // todo: when updating current note prevent save note watcher update, when its a mercure based update.
            // look into nextTick
            const updateNote = this.notes.find(note => note.id === eventData.id)

            updateNote.body = eventData.body
          }
        }
      })
  }
})