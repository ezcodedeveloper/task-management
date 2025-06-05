<script setup lang="ts">
import { ref, computed } from 'vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { onMounted } from 'vue';
import { useToast } from 'vue-toastification';
const toast = useToast();
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import Datepicker from 'vue-datepicker-next';
import 'vue-datepicker-next/index.css';
import 'vue-datepicker-next/locale/en.js';
interface Task {
    id: number;
    title: string;
    due_date: string;
    priority: string;
    status: string;
}
const searchTerm = ref('');

interface Task {
    id: number;
    title: string;
    due_date: string;
    priority: string;
    status: string;
}

const tasks = ref<Task[]>([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
})


const handleSubmit = async () => {
    errors.value = {
        title: !form.value.title ? 'Task title is required' : '',
        status: !form.value.status ? 'Task status is required' : '',
        priority: !form.value.priority ? 'Task priority is required' : '',
    };
    if (Object.values(errors.value).some((err) => err)) return
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch('/tasks/store-task', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form.value),
        });
        const data = await response.json();
        if (!response.ok) {
            //  error
            if (response.status === 422 && data.errors) {
                if (data.errors.title) {
                    errors.value.title = data.errors.title[0]; 
                }
                if (data.errors.status) {
                    errors.value.status = data.errors.status[0]; 
                }
                if (data.errors.priority) {
                    errors.value.priority = data.errors.priority[0]; 
                }
            }

            return; // Stop here after setting errors
        }
        // ✅ Show success toast
        toast.success(data.message)

        // ✅ Refresh tasks (call your fetch function)
        await fetchTasks()
        resetForm();
    } catch (error) {
        console.error('Error signing up:', error);
        // Handle network or other errors
    }
}
const fetchTasks = async (page = 1) => {
    const params = new URLSearchParams({
        page: page.toString(),
        search: searchTerm.value || '',
        per_page: '10'
    });

    const response = await fetch(`/tasks/get-tasks?${params.toString()}`);
    const data = await response.json();

    tasks.value = data.data;
    pagination.value = {
        current_page: data.current_page,
        last_page: data.last_page,
        total: data.total,
    };
};
const emit = defineEmits(['close', 'submitted']);
const form = ref({
    title: '',
    description: '',
    due_date: '',
    priority: '',
    status: '',
});
const errors = ref({
    title: '',
    priority: '',
    status: '',
})
const selectedDate = ref(null); // Initialize with null or a default date

const handleDateChange = (date: Date | string | null) => {
    console.log('Date selected (raw from component):', date);

};
const resetForm = () => {
    errors.value = {
        title: '',
        priority: '',
        status: '',
    };

    form.value = {
        title: '',
        description: '',
        due_date: '',
        priority: '',
        status: '',
    };

    selectedDate.value = null;
};


</script>
<style scoped>
.mx-datepicker {
    @apply w-full;
}

select {
    background-image: none;
}

select::-webkit-outer-spin-button,
select::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

select::-ms-expand {
    display: none;
}

.mx-datepicker {
    position: relative !important;
    z-index: 9999 !important;
   
}

.overlapping-element {
    pointer-events: none !important;
    z-index: 1 !important;
}

.mx-input-wrapper {
    position: relative !important;
    pointer-events: auto !important;
    z-index: 999 !important;
}

.invisible-blocker {
    pointer-events: none !important;
}

.mx-datepicker-popup {
    z-index: 99999999 !important;
    position: absolute;
    box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
   
}
</style>
<template>
    <Dialog -model:open="isModalOpen">
                        <DialogTrigger as-child>
                            <button
                                class="w-full sm:w-full md:w-50 lg:w-50 bg-black text-white hover:bg-black/90 cursor-pointer py-2 rounded-md">Add
                                Task</button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader class="space-y-3">
                                <DialogTitle>
                                    <h2 id="radix-«rb»" class="text-lg font-semibold leading-none tracking-tight">Add
                                        New Task</h2>
                                </DialogTitle>
                                <DialogDescription>
                                    <p id="radix-«rc»" class="text-sm text-muted-foreground">Create a new task with all
                                        the necessary
                                        details.</p>
                                </DialogDescription>
                            </DialogHeader>

                            <form @submit.prevent="handleSubmit">
                                <div class="grid gap-4 py-4">
                                    <div class="grid gap-2"><label
                                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            for="title">Title</label><input
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            id="title" placeholder="Enter task title" v-model="form.title">
                                        <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title }}</p>
                                    </div>
                                    <div class="grid gap-2"><label
                                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            for="description">Description</label><textarea
                                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            id="description" placeholder="Enter task description" rows="3"
                                            v-model="form.description"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="grid gap-2"><label
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                for="status">Status</label>
                                            <div class="">
                                                <div class="relative">
                                                    <select v-model="form.status"
                                                        class="w-full appearance-none bg-white border border-gray-200 rounded-md px-3 py-2.5 pr-8 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 cursor-pointer shadow-sm">
                                                        <option value="" disabled hidden selected>Select status</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Completed">Completed</option>
                                                    </select>

                                                    <!-- Dropdown chevron icon -->
                                                    <div
                                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5" d="M6 9l6 6 6-6"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p v-if="errors.status" class="mt-1 text-xs text-red-500">{{
                                                    errors.status }}</p>

                                            </div>
                                        </div>
                                        <div class="grid gap-2"><label
                                                class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                                for="priority">Priority</label>
                                            <div class="">
                                                <div class="relative">
                                                    <select v-model="form.priority"
                                                        class="w-full appearance-none bg-white border border-gray-200 rounded-md px-3 py-2.5 pr-8 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-300 focus:border-gray-300 cursor-pointer shadow-sm">
                                                        <option value="" disabled hidden selected>Select priority
                                                        </option>
                                                        <option value="Low">Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="High">High</option>
                                                    </select>

                                                    <!-- Dropdown chevron icon -->
                                                    <div
                                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5" d="M6 9l6 6 6-6"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p v-if="errors.priority" class="mt-1 text-xs text-red-500">{{
                                                    errors.priority }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid gap-2"><label
                                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                            for="dueDate">Due Date</label>
                                        <div class="w-full datepickersection"
                                            style="position: relative; z-index: 99999;">
                                            <Datepicker v-model="form.due_date" format="YYYY-MM-DD" :teleport="true" />
                                        </div>
                                    </div>


                                </div>
                                <div class="flex justify-end gap-2">
                                    <DialogClose as-child>
                                        <button
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-secondary text-secondary-foreground shadow-xs hover:bg-secondary/80 h-9 px-4 py-2 has-[>svg]:px-3">Cancel</button>
                                    </DialogClose>
                                    <button
                                        class="w-50 bg-black text-white hover:bg-black/90 cursor-pointer py-2 rounded-md">Submit</button>
                                </div>
                            </form>
                        </DialogContent>
                    </Dialog>
</template>



