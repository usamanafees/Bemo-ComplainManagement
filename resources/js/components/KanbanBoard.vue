<template>

<div>
  <div>
  <div class="flex items-center border-b border-teal-500 py-4">
    <label>Title: </label><input id="status_title" class="appearance-none bg-transparent border  w-17 text-gray-700 mr-3 py-1 px-2 leading-tight " v-model="newstatus.title" type="text" placeholder="Title" aria-label="Title">
    <label>Order: </label><input id="status_title" class="appearance-none bg-transparent border w-17 text-gray-700 mr-3 py-1 px-2 leading-tight " v-model="newstatus.order" type="number" placeholder="0"  aria-label="Title">
    <button @click.prevent="createStatus();" class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded" type="button">
      Add
    </button>
    <span id="ermsg" style="color:red">

    </span>
  </div>
  </div>
  <div class="relative p-2 flex overflow-x-auto h-full">
    <!-- Columns (Statuses) -->
    <div
      v-for="status in statuses"
      :key="status.slug"
      class="mr-6 w-4/5 max-w-xs flex-shrink-0"
    >
      <div class="rounded-md shadow-md overflow-hidden">
        <div class="p-3 flex justify-between items-baseline bg-gray-800 ">
          <h4 class="font-medium text-white">
            {{ status.title }}
          </h4>
          <button
            @click="openAddTaskForm(status.id)"
            class="py-1 px-2 text-sm text-orange-500 hover:underline"
          >
            Add Task
          </button>
          <button
            @click="deleteStatus(status.id)"
            class="py-1 px-2 text-sm text-orange-500 hover:underline"
          >
            Delete Column
          </button>        
          </div>
        <div class="p-2 bg-blue-100">
          <!-- AddTaskForm -->
          <AddTaskForm
            v-if="newTaskForStatus === status.id"
            :status-id="status.id"
            v-on:task-added="handleTaskAdded"
            v-on:task-canceled="closeAddTaskForm"
          />
          <!-- ./AddTaskForm -->

          <!-- Tasks -->
          <draggable
            class="flex-1 overflow-hidden"
            v-model="status.tasks"
            v-bind="taskDragOptions"
            @end="handleTaskMoved"
          >
            <transition-group
              class="flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto rounded shadow-xs"
              tag="div"
            >
              <div
                v-for="task in status.tasks"
                :key="task.id"
                class="mb-3 p-4 flex flex-col bg-white rounded-md shadow transform hover:shadow-md cursor-pointer"
              >
                <span class="block mb-2 text-xl text-gray-900">
                  {{ task.title }}
                </span>
                <button
                  @click="showtask(task.id)"
                  class="py-1 px-2 text-sm text-orange-500 hover:underline"
                >
                  Show detail
                </button>
              </div>
              <!-- ./Tasks -->
            </transition-group>
          </draggable>
          <!-- No Tasks -->
          <div
            v-show="!status.tasks.length && newTaskForStatus !== status.id"
            class="flex-1 p-4 flex flex-col items-center justify-center"
          >
            <span class="text-gray-600">No tasks yet</span>

          </div>
          <!-- ./No Tasks -->
        </div>
      </div>
    </div>
    <!-- ./Columns -->
  </div>
</div>
</template>

<script>
import draggable from "vuedraggable";
import AddTaskForm from "./AddTaskForm";
import VModal from 'vue-js-modal'
Vue.use(VModal)
export default {
  components: { draggable, AddTaskForm },
  props: {
    initialData: Array
  },
  data() {
    return {
    statuses: [],
    newstatus : {'title':'','order':''},
    err_msg:'', 
    newTaskForStatus: 0
    };
  },
  computed: {
    taskDragOptions() {
      return {
        animation: 200,
        group: "task-list",
        dragClass: "status-drag"
      };
    }
  },
  mounted() {
    // 'clone' the statuses so we don't alter the prop when making changes
    this.statuses = JSON.parse(JSON.stringify(this.initialData));
  },
  methods: {
    createStatus : function createStatus()
    {  var this_ = this;
      console.log(this_.newstatus);
      if(this_.newstatus['title']!='' && this_.newstatus['order']!= '')
      {
        axios.post('create-status',{title:this_.newstatus['title'],order:this_.newstatus['order']}).then(function(response){
            console.log(response.data);
            this_.statuses = response.data;
            });
        document.getElementById('ermsg').innerHTML = ''; 

      }else
      {
         document.getElementById('ermsg').innerHTML = ' fields can not be empty'; 
      }
        this_.newstatus['title'] = '';
        this_.newstatus['order'] = '';
    },
    showtask(id) {
      var  id_ = id;
      var this_= this;
      axios.post('task-details',{id:id_}).then(function(response){
      console.log(response.data);
        this_.$modal.show({
        template: `
          <div  class="mb-3 p-4 flex flex-col bg-white rounded-md shadow transform hover:shadow-md cursor-pointer">
            <h1>Description:</h1>
            <br>
            <p>{{text}}</p>
          </div>
        `,
          props: ['text']
          },
          { text:response.data['description'] }, 
          { height: 'auto' },         )
      });

    },
    openAddTaskForm(statusId) {
      this.newTaskForStatus = statusId;
    },
    closeAddTaskForm() {
      this.newTaskForStatus = 0;
    },
    handleTaskAdded(newTask) {
      // Find the index of the status where we should add the task
      const statusIndex = this.statuses.findIndex(
        status => status.id === newTask.status_id
      );

      // Add newly created task to our column
      this.statuses[statusIndex].tasks.push(newTask);

      // Reset and close the AddTaskForm
      this.closeAddTaskForm();
    },
    deleteStatus  : function deleteStatus(id)
    { var _this = this;
      var  id_ = id;
      console.log(id_);
        axios.post('delete-status',{id:id_}).then(function(response){
            console.log(response.data);
            _this.statuses = response.data;
           
            });
    },
    handleTaskMoved(evt) {
      axios.put("tasks/sync", { columns: this.statuses }).catch(err => {
        console.log(err.response);
      });
    }
  }
};
</script>

<style scoped>
.status-drag {
  transition: transform 0.5s;
  transition-property: all;
}
</style>
