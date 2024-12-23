<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Flashcard Jobs
        </h2>
      </template>
  
      <div class="p-6">
        <div v-for="job in jobs" :key="job.id" class="mb-4">
          <div class="p-4 border rounded-lg shadow bg-gray-50 dark:bg-gray-800">
            <h3 class="text-lg font-bold">{{ job.category }}</h3>
            <p>Total Requested: {{ job.total_requested }}</p>
            <p>Total Processed: {{ job.total_processed }}</p>
            <p>Created: {{ job.created_at }}</p>
            <p>Status: <span :class="statusClass(job.status)">{{ job.status }}</span></p>
            <Link
              v-if="job.status == 'finished'"
              :href="`/flashcards/${job.id}`"
              class="mt-2 inline-block px-4 py-2 bg-blue-500 text-white rounded"
            >
              View Flashcards
            </Link>
            <ProgressBar v-if="job.status == 'processing'" :value="(job.total_processed/job.total_requested) * 100" />
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { defineProps, onMounted } from "vue";
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { Link, usePoll } from '@inertiajs/vue3';
  
  const { start, stop } = usePoll(2000, {
    onFinish() {
        const pending = props.jobs.find((j) => j.status != 'finished' && j.status != 'error');
        if(!pending){
            stop();
        }
    }
  }, {
    autoStart: true,
  });

  onMounted(() => {
      document.addEventListener('visibilitychange', handleVisChange);
    });

    const handleVisChange = (e) => {
        if(!document.hidden){
            start();
        }
    }

  
  const props = defineProps({
    jobs: Array,
});

  const statusClass = (status) => {
    switch (status) {
      case "pending":
        return "text-yellow-500";
      case "processing":
        return "text-blue-500";
      case "finished":
        return "text-green-500";
      case "failed":
        return "text-red-500";
      default:
        return "";
    }
  };
  </script>
  