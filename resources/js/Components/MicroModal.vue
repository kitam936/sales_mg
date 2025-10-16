<script setup>
    import axios from 'axios';
    import { ref ,onMounted, reactive} from 'vue'
    import { Link} from '@inertiajs/vue3';

    const search = ref('')
    const customers = reactive({})

    // onMounted(()=> {
    //     axios.get('/api/user')
    //     .then( res => {
    //         console.log(res.data)
    //     })
    // })
    const isShow = ref(false)

    const toggleStatus = () => { isShow.value = !isShow.value}

    const searchCustomers = async() =>{
        try{
        await axios.get(`/api/searchCustomers/?search=${search.value}`)
            .then(res=>{
                console.log(res.data)
                customers.value = res.data
            })
            toggleStatus()
        }catch(e){
            console.log(e.message)
        }
    }

    const emit = defineEmits(['update:customerId'])

    const setCustomer = e => {
        search.value = e.kana
        emit('update:customerId',e.id,e.name)
        toggleStatus()
    }

</script>

<template>
    <div v-show="isShow" class="modal " id="modal-1" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
      <div class="modal__container w-2/3" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
        <header class="modal__header">
          <h2 class="modal__title" id="modal-1-title">
            顧客検索
          </h2>
          <button @click="toggleStatus" type="button" class="modal__close" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content" id="modal-1-content">
        <div v-if="customers.value">
            <div class=" mx-auto sm:px-4 lg:px-4 border ">
                <table class="bg-white table-auto w-full text-center whitespace-no-wrap">
                    <thead>
                        <tr>
                            <th class="w-1/13 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">id</th>
                            <th class="w-1/13 md:1/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">name</th>
                            <th class="w-3/13 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">kana</th>
                            <th class="w-3/13 md:3/13 md:px-4 py-1 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">Tel</th>

                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="customer in customers.value.data" :key="customer.id">
                            <td class="border-b-2 boder-gray-200">
                                <button type="button" @click="setCustomer({ id:customer.id, kana:customer.kana, name:customer.name})" class="text-indigo-500" >
                                    {{ customer.id }}
                                </button>
                            </td>
                            <td class="border-b-2 boder-gray-200">{{ customer.name }} </td>
                            <td class="border-b-2 boder-gray-200">{{ customer.kana }} </td>
                            <td class="border-b-2 boder-gray-200">{{ customer.tel }} </td>

                        </tr>
                    </tbody>
                </table>
                </div>
        </div>
        </main>
        <footer class="modal__footer">
          <button @click="toggleStatus" type="button" class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
        </footer>
      </div>
    </div>
  </div>
  <div class="flex">
  <div>
  <input name="customer" v-model="search" class="w-60 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
  </div>
  <div class="ml-2">
    <button type="button" class="flex mx-auto text-white bg-teal-500 border-0 py-2 px-8 focus:outline-none hover:bg-teal-600 rounded text-lg" @click="searchCustomers" data-micromodal-trigger="modal-1" >検索する</button>
  </div>
</div>
</template>
