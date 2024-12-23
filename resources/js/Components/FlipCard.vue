<script setup>
    import { ref, toRef, Transition, watch } from 'vue';

    const props = defineProps({
        card: Object,
    });

    const propCard = toRef(props, 'card');
  

    watch(propCard, (newCard) => {
        flipped.value = false;
    });

    const flipped = ref(false);

    function toggleCard() {
        flipped.value = !flipped.value
    }

</script>


<template>
    <Transition @click="toggleCard()" mode="out-in">
        <div v-bind:key="flipped" class="card">
            <slot v-if="!flipped" name="front"></slot>
            <slot v-else name="back"></slot>
        </div>
    </Transition>
</template>

<style>
    .v-enter-active,
    .v-leave-active {
        transition: transform 200ms;
    }

    .v-enter-from,
    .v-leave-to {
        transform: rotateY(90deg);
    }
</style>