<template>
  <Head :title="note.title" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-row items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Edit Note: {{ note.title }}
        </h2>
        <div class="ml-auto flex gap-2">
          <Button class="!mt-0" @click="createFlashCards" label="Create flashcards" />
          <Button class="!mt-0" icon="pi pi-pencil" label="Edit Title & Category" @click="showDrawer = true" />
        </div>
      </div>

    </template>

    <div class="p-12">
      <div class="flex flex-row-reverse pb-2">
        <div class="flex flex-row gap-2 items-center">
          <span>Auto-save</span>
          <ToggleSwitch
            v-model="autoSaveEnabled" 
            class="!mt-0" 
            @change="toggleAutoSave"
          />
        </div>
      </div>

      <!-- Note Edit Form (Main Content - Quill Editor) -->
      <form @submit.prevent="submitForm" class="flex flex-col gap-4">
        <div class="text-gray-300">
          <!-- Quill Editor -->
          <quill-editor style="height: calc(100vh - 25rem)" @textChange="autoSave" ref="quill" v-model="form.content" class="quill-editor" theme="snow"></quill-editor>
        </div>

        <Button type="submit" class="mt-4 p-2 bg-blue-500 text-white rounded">
          Save Changes
        </Button>
      </form>
    </div>

    <!-- PrimeVue Drawer for Title and Category -->
    <Drawer
      v-model:visible="showDrawer"
      position="right"
      :modal="true"
      class="w-1/3 sm:w-1/4"
      :show-header="false"
    >
      <div class="p-4">
        <h3 class="text-white text-lg font-semibold">Edit Title & Category</h3>

        <!-- Title and Category Inputs -->
        <form @submit.prevent="submitForm" class="flex flex-col gap-4">
          <div class="text-gray-300 flex flex-col">
            <label for="title">Title</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="border p-2 rounded bg-gray-800"
            />
          </div>

          <div class="text-gray-300 flex flex-col">
            <label for="category">Category</label>
            <input
              id="category"
              v-model="form.category"
              required
              class="border p-2 rounded bg-gray-800"
            />
          </div>

          <Button type="submit" class="mt-4 p-2 bg-blue-500 text-white rounded">
            Save Changes
          </Button>
        </form>
      </div>
    </Drawer>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { debounce } from 'lodash'; // Import debounce from lodash
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useToast } from "vue-toastification";

const toast = useToast();


const props = defineProps({
  note: Object
});

const form = useForm({
  title: props.note.title,
  category: props.note.category,
  content: props.note.content // This is the Quill Delta
});

const quill = ref(null);
const showDrawer = ref(false);

// State to control Drawer visibility
const autoSaveEnabled = ref(false); // Track the state of auto-save

// Initialize Quill editor on mounted
onMounted(() => {
  quill.value.setContents(JSON.parse(props.note.content).ops);
});



const createFlashCards = () => {
    router.post(route('flashcards.jobs.create'), {
      category: form.category,
      count: 5,
      dryRun: true
    }, {
      onSuccess: (res) => {
        toast.success("Flashcards are being created!", {
          timeout: 2000,
          toastClassName: "custom-toast-class"
        });
      }
    })
  }

// Debounced function to save the note after a delay of 3 seconds
const debouncedSave = debounce(() => {
  if(!autoSaveEnabled) return;

  const contentDelta = quill.value.getContents();
  form.content = JSON.stringify(contentDelta);

  form.put(route('notes.update', props.note.id), {
    onSuccess: () => {
      // Optionally, display a success message
    
    }
  });
}, 2000); // 3-second debounce

// Watch the content to trigger auto-save when it's modified and auto-save is enabled
const autoSave = (newContent) => {
  if (autoSaveEnabled.value) {
    debouncedSave();
  }
};

// Submit the form manually (Save on button click)
const submitForm = () => {
  showDrawer.value = false;
  const contentDelta = quill.value.getContents();
  form.content = JSON.stringify(contentDelta);

  form.put(route('notes.update', props.note.id), {
    onSuccess: () => {
      // Optionally, display a success message
      console.log('Note saved successfully!');
    }
  });
};

// Toggle Auto-Save feature on/off
const toggleAutoSave = (e) => {
  if (autoSaveEnabled.value) {
    autoSave();
  } else {
    console.log('Auto-Save Disabled');
  }
};
</script>

<style scoped>
.quill-editor {
  min-height: 400px;
  border: 1px solid #ccc;
  margin-bottom: 10px;
  min-height: 75%;
}

input,
select,
button {
  margin-top: 10px;
}

.fixed {
  position: fixed;
}

.bottom-4 {
  bottom: 1rem;
}

.right-4 {
  right: 1rem;
}
</style>
