<style scoped>
  .modal-backdrop {
    position: fixed;
    left: 0;
    top: 0;

    z-index: 500;

    width: 100vw;
    height: 100vh;

    background: rgba(0, 0, 0, 0.2);

    display: grid;
    place-items: center;
  }

  .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 40%;
  }
  .modal-header,
  .modal-footer {
    padding: 15px;
    display: flex;
  }

  .modal-header {
    position: relative;
    border-bottom: 1px solid #eeeeee;
    color: #4AAE9B;
    justify-content: space-between;
  }

  .modal-footer {
    border-top: 1px solid #eeeeee;
    flex-direction: column;
    justify-content: flex-end;
  }

  .modal-body {
    position: relative;
    padding: 20px 10px;
  }

  .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    border: none;
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    color: #4AAE9B;
    background: transparent;
  }

  .btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
  }

  .modal-fade-enter-from,
  .modal-fade-leave-to {
    opacity: 0;
  }

  .modal-fade-enter-active,
  .modal-fade-leave-active {
    transition: 0.25s ease all;
  }
</style>

<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div class="modal-backdrop" v-show="visible">
        <div class="modal-content" ref="modalContent">
          <header class="modal-header">
            <slot name="header">
              This is the default title!
            </slot>
          </header>

          <section class="modal-body">
            <slot name="body">
              This is the default body!
            </slot>
          </section>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
  export default {
    props: ["visible"],
    mounted() {
      document.addEventListener("click", this.handleClickOutside);
    },
    beforeUnmount() {
      document.removeEventListener("click", this.handleClickOutside);
    },
    methods: {
      handleClickOutside(event) {
        if (event.target && !this.$refs.modalContent.contains(event.target)) {
          this.$emit("close-modal");
        }
      }
    }
  };
</script>
