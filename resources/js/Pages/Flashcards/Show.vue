<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Flashcards for {{ job.category }}
        </h2>
      </template>
  
      <div class="p-6 flex gap-6 h-full-content overflow-hidden">
        <!-- Left Column: Scrollable List of Questions -->
        <div class="w-1/3 max-h-full overflow-y-auto bg-gray-100 dark:bg-gray-900 p-4 rounded-lg custom-scrollbar">
          <ul>
            <li
              v-for="flashcard in flashcards"
              :key="flashcard.id"
              :class="{'bg-gray-200 dark:bg-gray-700': selectedCard === flashcard}"
              class="cursor-pointer p-2 hover:bg-gray-200 dark:hover:bg-gray-700 rounded"
              @click="selectCard(flashcard)"
            >
              {{ flashcard.question }}
            </li>
          </ul>
        </div>
  
        <!-- Vertical Divider -->
        <div class="w-px bg-gray-300 dark:bg-gray-700 h-full-content"></div>
  
        <!-- Right Column: Flashcard Display -->
        <div class="w-2/3 flex items-center justify-center flex-grow">
            <FlipCard      
                :card="selectedCard"       
                v-if="selectedCard"
                class="relative w-96 h-64 border rounded-lg shadow perspective"
            >
                <template v-slot:front>
                    <div class="absolute w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-lg font-bold">{{ selectedCard.question }}</h3>
                    </div>
                </template>
                <template v-slot:back>
                    <div class="absolute w-full h-full flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-lg">{{ selectedCard.answer }}</p>
                        <p class="mt-2 text-sm text-gray-500">(Click to flip back)</p>
                    </div>
                </template>
            </FlipCard>
          <div v-else>
            <p class="text-lg text-gray-500 dark:text-gray-400">Select a card to view it here.</p>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
<script setup>
  import { ref } from "vue";
  import { defineProps } from "vue";
  import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
  import FlipCard from '@/Components/FlipCard.vue';
  
  const props = defineProps({
    job: Object,
    flashcards: Array,
  });
  
  const selectedCard = ref(null);
  
  function selectCard(card) {
    selectedCard.value = card;
  }
</script>
  
<style scoped>
::-webkit-scrollbar {
  width: 10px;
}

/* Track */
::-webkit-scrollbar-track {
  background: rgb(17 24 39 / var(--tw-bg-opacity));
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: rgb(31 41 55 / var(--tw-bg-opacity, 1));
  border-radius: 10px;
 
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555; 
}
</style>
  