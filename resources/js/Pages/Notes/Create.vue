<template>
    <AuthenticatedLayout>
      <Head title="New Note" />
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-400 dark:text-gray-200">
          Create New Note
        </h2>
      </template>
  
      <form @submit.prevent="submit" class="max-w-4xl mx-auto space-y-4">
        <!-- Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-400">Title</label>
          <input
            v-model="form.title"
            type="text"
            id="title"
            class="text-black mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
          <span v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</span>
        </div>
  
        <!-- Category -->
        <div>
          <label for="category" class="block text-sm font-medium text-gray-400">Category</label>
          <input
            v-model="form.category"
            id="category"
            class="text-black mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
          </input>
          <span v-if="form.errors.category" class="text-red-500 text-sm">{{ form.errors.category }}</span>
        </div>
  
        <Button
          type="submit">
          Save Note
        </Button>
      </form>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { useForm, Head } from "@inertiajs/vue3";
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

  
  const form = useForm({
    title: "",
    category: "",
  });
  
  const submit = () => {
    form.post(route("notes.store"), {
      onSuccess: (data) => {
        // console.log(data);
        // window.location.href = route("notes.edit", { note: data.id });
      },
    });
  };
  </script>
  
  <style scoped>
  /* Add any specific styles */
  </style>
  