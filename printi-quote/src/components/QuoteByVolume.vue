<template>
  <main class="quote-by-volume">
    <div class="container-fluid">
      <div v-if="delivery_options.length === 0" class="row justify-content-center">
        <div class="col col-md-3">
          <form v-on:submit.prevent="calculate" class="qbv-form">
            <div class="form-row">
              <div class="col">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.origin_zip_code.$dirty && $v.origin_zip_code.$invalid }"
                    @input="$v.origin_zip_code.$touch"
                    v-model="origin_zip_code"
                    v-mask="'#####-###'"
                    type="text"
                    placeholder="CEP de Origem"
                    class="form-control" />
                </div>
              </div>
              <div class="col-xs-12 col">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.destination_zip_code.$dirty && $v.destination_zip_code.$invalid }"
                    @input="$v.destination_zip_code.$touch"
                    v-model="destination_zip_code"
                    v-mask="'#####-###'"
                    type="text"
                    placeholder="CEP de Destino"
                    class="form-control" />
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.cost_of_goods.$dirty && $v.cost_of_goods.$invalid }"
                    @input="$v.cost_of_goods.$touch"
                    v-model="cost_of_goods"
                    v-money="money"
                    type="text"
                    placeholder="Preço dos produtos"
                    class="form-control" />
                </div>
              </div>
              <div class="col">
                <span class="cost-of-goods__label">(Preço dos produtos)</span>
              </div>
            </div>

            <div class="form-row">
              <div class="col-6">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.weight.$dirty && $v.weight.$invalid }"
                    @input="$v.weight.$touch"
                    v-model="weight"
                    v-mask="['# kg','## kg','### kg']"
                    type="text"
                    placeholder="Peso"
                    class="form-control" />
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="col-6">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.width.$dirty && $v.width.$invalid }"
                    @input="$v.width.$touch"
                    v-model="width"
                    v-mask="['# cm','## cm','### cm','#### cm']"
                    type="text"
                    placeholder="Largura"
                    class="form-control" />
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.height.$dirty && $v.height.$invalid }"
                    @input="$v.height.$touch"
                    v-model="height"
                    v-mask="['# cm','## cm','### cm','#### cm']"
                    type="text"
                    placeholder="Altura"
                    class="form-control" />
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <input
                    :class="{ 'is-invalid': $v.length.$dirty && $v.length.$invalid }"
                    @input="$v.length.$touch"
                    v-model="length"
                    v-mask="['# cm','## cm','### cm','#### cm']"
                    type="text"
                    placeholder="Comprimento"
                    class="form-control" />
                </div>
              </div>
            </div>

            <div class="form-row">
              <button :disabled="this.$v.$invalid" type="submit" class="btn btn-block btn-primary">Calcular frete</button>
            </div>

          </form>
        </div>
        <div class="col-3 d-none d-md-block">
          <div class="qbv-explain">
            <img src="@/assets/box.png" alt="sample box" />
          </div>
        </div>
      </div><!-- -->
      <div v-if="delivery_options.length >= 1" class="row justify-content-center">
        <div class="col col-md-5">
          <section class="qbv-results">
            <h5 class="">Opções de envio encontradas para você!</h5>
            <div class="box">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <td>#</td>
                    <td>Tempo entrega</td>
                    <td>Custo</td>
                    <td>Serviço</td>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="option in delivery_options"
                    :key="option.delivery_method_id"
                    :class="{'selected': arr_selected_delivery == option.delivery_method_id, 'text-black-50': arr_selected_delivery.length === 1 && option.delivery_method_id != arr_selected_delivery }">
                    <td>
                      <input
                        :disabled="arr_selected_delivery.length === 1 && option.delivery_method_id != arr_selected_delivery"
                        v-bind:value="option.delivery_method_id"
                        v-model="arr_selected_delivery"
                        @click="setSelectedDelivery(option)"
                        type="checkbox" />
                    </td>
                    <td>{{ option.delivery_estimate_business_days }} dias</td>
                    <td>{{ formatPrice(option.final_shipping_cost, 'R$') }}</td>
                    <td>{{ option.description }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button
              :disabled="arr_selected_delivery == false"
              @click="alertSelected"
              class="btn btn-primary btn-choose">Ok</button>
              <button @click="goBack" class="btn btn-secondary btn-back">Voltar</button>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import {VMoney} from 'v-money'
import {mask} from 'vue-the-mask'
import { required, minLength } from 'vuelidate/lib/validators'

export default {
  directives: {
    mask,
    money: VMoney
  },
  name: 'QuoteByVolume',
  data () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: 'R$ ',
        suffix: '',
        precision: 2,
        masked: false
      },
      delivery_options: [],
      selected_delivery: {},
      arr_selected_delivery: [], // need be refactored o/
      origin_zip_code: '',
      destination_zip_code: '',
      weight: null,
      cost_of_goods: null,
      width: null,
      height: null,
      length: null
    }
  },
  validations: {
    origin_zip_code: {
      required,
      minLength: minLength(9)
    },
    destination_zip_code: {
      required,
      minLength: minLength(9)
    },
    weight: {
      required,
      minLength: minLength(1)
    },
    cost_of_goods: {
      required,
      minLength: minLength(1)
    },
    width: {
      required,
      minLength: minLength(1)
    },
    height: {
      required,
      minLength: minLength(1)
    },
    length: {
      required,
      minLength: minLength(1)
    }
  },
  methods: {
    calculate () {
      if (!this.$v.$invalid) {
        this.axios.post(process.env.API_BASE, {
          origin_zip_code: this.origin_zip_code,
          destination_zip_code: this.destination_zip_code,
          weight: this.replace(this.weight, ' kg'),
          cost_of_goods: this.replace(this.cost_of_goods, 'R$#.###,##'),
          width: this.replace(this.width, ' cm'),
          height: this.replace(this.height, ' cm'),
          length: this.replace(this.length, ' cm')
        }).then((response) => {
          this.delivery_options = response.data.content.delivery_options
        }).catch(error => {
          alert(error)
        })
      }
    },
    setSelectedDelivery (option) {
      this.selected_delivery = option
    },
    alertSelected () {
      var days = this.selected_delivery.delivery_estimate_business_days
      var cost = this.formatPrice(this.selected_delivery.final_shipping_cost, 'R$')
      var description = this.selected_delivery.description
      var msg = description + ', R$ ' + cost + ', ' + days + ' dia(s)'

      alert(msg)
    },
    goBack () {
      window.location.reload(true)
    },
    replace (str, needle) {
      if (needle === 'R$#.###,##') {
        str = str.replace('.', '', (match) => {
          return match
        })
        str = str.replace(',', '.', (match) => {
          return match
        })
        return str.replace('R$ ', '', (match) => {
          return match
        })
      }
      return str.replace(new RegExp(needle, 'g'), '', (match) => {
        return match
      })
    },
    formatPrice (value, currency) {
      let val = (value / 1).toFixed(2).replace('.', ',')
      return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
    }
  }
}
</script>

<style lang="scss">
.quote-by-volume{
  padding: 3rem 0;
}
.qbv-form .btn,
.qbv-form .form-control,
.qbv-form .input-group-text{
  font-size: 0.8rem;
}
.qbv-form .btn{
  margin: 0 auto;
  width: 95%;
}
.cost-of-goods__label{
  font-size: .8rem;
  font-style: italic;
}
.qbv-explain img{
  width: 100%;
}
.qbv-results{
  font-size: .8rem;
  text-align: center;
}
.qbv-results h5{
  margin-bottom: 2rem;
}
.qbv-results .box{
  padding: .5rem;
  background: #fff;
  border-radius: 5px;
  box-shadow: 1px 2px 2px #ccc;
  margin-bottom: 1rem;
}
.qbv-results .table thead{
  background: #007bff;
  border-radius: 5px;
  color: #fff;
  font-weight: bold;
}
.qbv-results .selected{
  background-color: rgba(0, 0, 0, 0.075);
}
.qbv-results .btn-back,
.qbv-results .btn-choose{
  font-size: .8rem;
}
</style>
