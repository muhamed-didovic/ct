<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Products</div>

                    <div class="card-body">
                        <form action="" @submit.prevent="productSubmit">
                            <!--<div class="form-group row">-->
                                <!--<label for="name" class="col-sm-4 col-form-label text-md-right">Product index</label>-->

                                <!--<div class="col-md-6">-->
                                    <!--<input id="number" type="text"-->
                                           <!--class="form-control"-->
                                           <!--v-model="form.number"-->
                                           <!--name="number" required autofocus>-->
                                <!--</div>-->
                            <!--</div>-->
                            <input type="hidden" name="number" id="number" v-model="form.number">
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
                            <li v-for="(product, index) in products">
                                <!--Index: {{index}} <br>-->
                                Product Name: {{product.name}} <br>
                                Quantity in stock: {{product.quantity}} <br>
                                Price per item: {{product.price}} <br>
                                Datetime submitted: {{product.submitted}} <br>
                                Total value number: {{product.total}} <br>
                                <button @click.prvent="editProduct(index, product)">Edit Product</button>
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
                    number: '',
                    name: '',
                    quantity: '',
                    price: '',
                },
                total: 0,
                products: []
            };
        },
        computed: {
            sortedItems: function() {
                return this.products.sort((a, b) => {
                    if (Date.parse(a.submitted) > Date.parse(b.submitted)) {
                        return 1
                    } else if (Date.parse(a.submitted) < Date.parse(b.submitted)) {
                        return -1
                    } else {
                        return 0
                    }
                })
            }
        },
        mounted() {
            axios.get('/list')
                .then(response => {
                    this.total = response.data.total
                    let products =  Object.keys(response.data.products).map(i => response.data.products[i]);
                    console.log('prod', _.orderBy(products, ['submitted'], [ 'desc']));
                    this.products =  _.orderBy(products, ['submitted'], [ 'desc'])

                })
                .catch(err => console.log('Error from methods:', err));
        },
        methods: {
            editProduct(index, product) {
                this.form.number = index
                this.form.name = product.name
                this.form.quantity = product.quantity
                this.form.price = product.price
            },
            productSubmit() {
                console.log('2222', this.form);
                axios.post('/products', this.form)
                    .then(response => {
                        console.log('response:', response);

                        this.total = response.data.total
                        this.form.number = ''
                        this.form.name = ''
                        this.form.quantity = ''
                        this.form.price = ''

                        let products =  Object.keys(response.data.products).map(i => response.data.products[i]);
                        console.log('prod', _.orderBy(products, ['submitted'], [ 'desc']));
                        this.products =  _.orderBy(products, ['submitted'], [ 'desc'])

                    })
                    .catch(err => console.log('Error from methods:', err));
            },
        }
    }
</script>
