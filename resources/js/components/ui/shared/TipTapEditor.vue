/**
 * @component TipTapEditor
 * @description Rich text editor using TipTap with toolbar for bold and lists
 */

<template>
  <div class="tiptap-editor">
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-1 p-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 rounded-t-xl">
      <!-- Bold -->
      <button
        type="button"
        @click="editor?.chain().focus().toggleBold().run()"
        :class="[
          'p-2 rounded-lg transition-colors duration-200',
          editor?.isActive('bold')
            ? 'bg-blue-500 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
        ]"
        title="Bold (Ctrl+B)"
      >
        <Bold class="w-4 h-4" />
      </button>

      <!-- Italic -->
      <button
        type="button"
        @click="editor?.chain().focus().toggleItalic().run()"
        :class="[
          'p-2 rounded-lg transition-colors duration-200',
          editor?.isActive('italic')
            ? 'bg-blue-500 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
        ]"
        title="Italic (Ctrl+I)"
      >
        <Italic class="w-4 h-4" />
      </button>

      <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>

      <!-- Bullet List -->
      <button
        type="button"
        @click="editor?.chain().focus().toggleBulletList().run()"
        :class="[
          'p-2 rounded-lg transition-colors duration-200',
          editor?.isActive('bulletList')
            ? 'bg-blue-500 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
        ]"
        title="Bullet List"
      >
        <List class="w-4 h-4" />
      </button>

      <!-- Ordered List -->
      <button
        type="button"
        @click="editor?.chain().focus().toggleOrderedList().run()"
        :class="[
          'p-2 rounded-lg transition-colors duration-200',
          editor?.isActive('orderedList')
            ? 'bg-blue-500 text-white'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'
        ]"
        title="Numbered List"
      >
        <ListOrdered class="w-4 h-4" />
      </button>
    </div>

    <!-- Editor Content -->
    <EditorContent 
      :editor="editor" 
      class="prose prose-sm dark:prose-invert max-w-none p-4 min-h-[300px] bg-gray-50 dark:bg-gray-900 rounded-b-xl border border-t-0 border-gray-200 dark:border-gray-700 focus-within:ring-2 focus-within:ring-blue-500"
    />

    <!-- Character Count -->
    <p v-if="editor" class="text-xs text-gray-500 dark:text-gray-400 mt-2">
      {{ editor.storage.characterCount?.characters() || 0 }} karakter
    </p>
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import CharacterCount from '@tiptap/extension-character-count'
import { Bold, Italic, List, ListOrdered } from 'lucide-vue-next'
import { watch, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Tulis konten berita di sini...'
  }
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Placeholder.configure({
      placeholder: props.placeholder,
    }),
    CharacterCount,
  ],
  editorProps: {
    attributes: {
      class: 'outline-none min-h-[280px]',
    },
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
})

// Sync external changes
watch(() => props.modelValue, (newValue) => {
  if (editor.value && newValue !== editor.value.getHTML()) {
    editor.value.commands.setContent(newValue, false)
  }
})

onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style>
/* TipTap Editor Styles - Match public page styling */
.tiptap-editor .ProseMirror {
  outline: none;
  color: #374151; /* gray-700 */
  line-height: 2;
  text-align: justify;
  hyphens: auto;
  -webkit-hyphens: auto;
}

/* Dark mode text color */
.dark .tiptap-editor .ProseMirror {
  color: #e5e7eb; /* gray-200 */
}

.tiptap-editor .ProseMirror p {
  margin-bottom: 1.5rem;
  line-height: 2;
  text-align: justify;
}

.tiptap-editor .ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}

.tiptap-editor .ProseMirror ul {
  list-style-type: disc;
  padding-left: 2rem;
  margin: 1.5rem 0;
}

.tiptap-editor .ProseMirror ol {
  list-style-type: decimal;
  padding-left: 2rem;
  margin: 1.5rem 0;
}

.tiptap-editor .ProseMirror li {
  margin: 0.5rem 0;
  line-height: 1.75;
}

.tiptap-editor .ProseMirror li p {
  margin: 0;
  display: inline;
}

.tiptap-editor .ProseMirror strong {
  font-weight: 600;
  color: #1f2937;
}

.tiptap-editor .ProseMirror em {
  font-style: italic;
}
</style>
