<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Products</div>

                    <div class="card-body">
                        <form action="" @submit.prevent="onSchemaFormSubmit">
                            <!--@csrf-->

                            <div class="form-group row">
                                <label for="name" class="col-sm-4 col-form-label text-md-right">Product name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control"
                                           v-model="form.name"
                                           name="name" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="quantity" class="col-md-4 col-form-label text-md-right">Quantity in
                                    stock</label>

                                <div class="col-md-6">
                                    <input id="quantity" type="number"
                                           class="form-control"
                                           v-model="form.quantity"
                                           name="quantity" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="price" class="col-md-4 col-form-label text-md-right">Price per item</label>

                                <div class="col-md-6">
                                    <input id="price" type="number"
                                           class="form-control"
                                           v-model="form.price"
                                           name="price" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary submit">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p>Total sum of all products is: {{(total > 0) ? total : 0}}</p>
                        <ul>
                            <li v-for="product in products">
                                Product Name: {{product.name}} <br>
                                Quantity in stock: {{product.quantity}} <br>
                                Price per item: {{product.price}} <br>
                                Datetime submitted: {{product.submitted}} <br>
                                Total value number: {{product.total}} <br>
                                <hr>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                form: {
                    name: '',
                    quantity: '',
                    price: '',
                },
                total: 0,
                products: []
            };
        },
        mounted() {
            axios.get('/list')
                .then(response => {
                    console.log('rrrrr', response.data);

                    this.products = response.data.products
                    this.total = response.data.total

                })
                .catch(err => console.log('Error from methods:', err));
        },
        methods: {
            onSchemaFormSubmit() {
                // const r =  _.reduce(this.data, (result, item , key) => {
                //     console.log('res', result, 'key', key, 'item', item);
                //     result[key] = item.value;
                //     return result;
                // }, {});
                console.log('2222', this.form);
                axios.post('/products', this.form)
                    .then(response => {
                        console.log('response:', response);
                        this.products = response.data.products
                        this.total = response.data.total
                        this.form.name = ''
                        this.form.quantity = ''
                        this.form.price = ''

                    })
                    .catch(err => console.log('Error from methods:', err));
            },
        }
    }
</script>
