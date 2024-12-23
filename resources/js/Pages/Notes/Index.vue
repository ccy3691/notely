<template>
    <Head title="Notes" />
  
    <AuthenticatedLayout>
      <template #header>
        <div class="flex flex-row items-center">
          <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Notes
          </h2>
          <Button class="ml-auto m-0">
            <Link
            :href="route('notes.create')"
            class="m-0"
            >
            New Note
          </Link>
        </Button>
      </div>
      </template>
  
      <div class="container mx-auto p-6">
  
        <!-- Search Notes -->
        <ais-instant-search :search-client="searchClient" :index-name="'notes:updated_at:desc'">

          <!-- <ais-configure :filters="`user_id = ${$page.props.auth.user.id}`" /> -->
          <div class="flex flex-row gap-4">
            <!-- Improved Search Box -->
            <ais-search-box class="text-black mb-2 w-full" placeholder="Search notes...">
              <template v-slot="{ currentRefinement, isSearchStalled, refine }">
                <input
                  type="search"
                  class="w-full text-black mt-1 block border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                  placeholder="Search notes..."
                  :value="currentRefinement"
                  @input="refine($event.currentTarget.value)"
                >
                <span :hidden="!isSearchStalled">Loading...</span>
              </template>  
            </ais-search-box>
            <ais-sort-by
              ref="sort_by"
              class="text-black ml-auto rounded-lg"
              :items="[
                { value: 'notes:updated_at:desc', label: 'Updated At Newest' },
                { value: 'notes:updated_at:asc', label: 'Updated At Oldest' }
              ]"
            />
          </div>
          
          <!-- Search Results -->
          <ais-hits>
            <template #default="{ items }">
              <div v-if="items.length" class="space-y-4">
                <div v-for="note in items" :key="note.id" class="note-card cursor-pointer" @click="router.get(route('notes.edit', note.id))">
                  <h3 v-html="note._highlightResult.title.value" class="text-xl font-semibold text-gray-800"></h3>
                  <p v-html="note._highlightResult.content_html.value" class="text-gray-600 mt-2 break-all line-clamp-6"></p>
                  <p v-html="`Category: ${note._highlightResult.category.value}`" class="text-sm text-gray-500 mt-2"></p>
                </div>
              </div>
              <p v-else class="text-center text-gray-500">No results found.</p>
            </template>
          </ais-hits>
        </ais-instant-search>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { Link, router, Head } from '@inertiajs/vue3'
  import { instantMeiliSearch } from "@meilisearch/instant-meilisearch";
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
  import { onMounted, useTemplateRef } from 'vue';

  const props = defineProps({
    search_token: String,
  });

  const searchClient = instantMeiliSearch(import.meta.env.VITE_MEILISEARCH_HOST, props.search_token, {
    primaryKey: "id",
    meiliSearchParams: {
      attributesToCrop: ['content_html', 'content'],
      attributesToRetrieve: ['content_html', 'content', 'id', 'user_id'],
      attributesToHighlight: ['title', 'content_html', 'content', 'category', 'user_id'],
      cropLength: 10
    },
  }).searchClient;

  </script>
  
  <style scoped>
  .container {
    max-width: 800px;
    margin: 0 auto;
  }

  .ais-SearchBox-input>input{
    width: 100px !important;
  }

  
  .note-card {
    padding: 16px;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
  }

  
  .ais-Highlight-highlighted {
    background-color: yellow;
    font-weight: bold;
  }
  
  .search-box {
    width: 100%;
    padding: 12px 16px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s ease;
  }

  .quill-editor {
  min-height: 200px;
  border: 1px solid #ddd;
  padding: 10px;
  margin-bottom: 10px;
}
  
  .search-box:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
  }
  
  .search-box::placeholder {
    color: #6B7280;
  }
  
  button {
    cursor: pointer;
  }
  </style>
  
  <style>
    select.ais-SortBy-select{
    border-radius: 8px;
    margin-top: .25rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
  }
  </style>
  