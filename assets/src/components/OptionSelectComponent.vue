<template>

    <div>
        <div class="product-info-counter">
            <div v-if="!onlyOneOption" class="product-info-cell" v-for="(selectedOption, key) in select">
                <div class="product-info-counter-title">{{selectedOption.unit.name}} </div>
                <div v-for="(v, k) in selectedOption.custom_values" style="padding-left: 10px">{{ k }} : {{ v }}</div>
                <div class="xe-spin-box">
                    <button type="button" @click="selectedOption.count--; if(selectedOption.count<=0)dropOption(key)"><i class="xi-minus-thin"></i><span class="xe-sr-only">감소</span></button>
                    <p>{{selectedOption.count}}</p>
                    <button type="button" @click="selectedOption.count++"><i class="xi-plus-thin"></i><span class="xe-sr-only">증가</span></button>
                </div>
                <p class="product-info-counter-sum">{{(selectedOption.unit.sell_price * selectedOption.count).toLocaleString()}}원</p>
                <button class="xe-btn xe-btn-remove" @click="dropOption(key)"><i class="xi-close-thin"></i><span class="xe-sr-only">이 옵션 삭제</span></button>
            </div> <!-- //product-info-cell -->

            <div v-if="onlyOneOption" class="product-info-cell" v-for="(selectedOption, key) in select">
                <div class="product-info-counter-title">{{selectedOption.unit.name}} </div>
                <div v-for="(v, k) in selectedOption.custom_values" style="padding-left: 10px">{{ k }} : {{ v }}</div>
                <div class="xe-spin-box">
                    <button type="button" @click="(selectedOption.count>1) ? selectedOption.count-- : ''"><i class="xi-minus-thin"></i><span class="xe-sr-only">감소</span></button>
                    <p>{{selectedOption.count}}</p>
                    <button type="button" @click="selectedOption.count++"><i class="xi-plus-thin"></i><span class="xe-sr-only">증가</span></button>
                </div>
                <p class="product-info-counter-sum">{{(selectedOption.unit.sell_price * selectedOption.count).toLocaleString()}}원</p>
            </div>

        </div> <!-- //product-info-counter -->

        <p class="price-sum"><span class="text">총 상품금액</span> {{totalChoosePrice.toLocaleString()}}<i>원</i></p>
    </div>
</template>

<script>
    export default {
        name: "OptionSelectComponent",
        watch: {
            selectOption(el) {
                if (el == null) return
                var exist = this.select.find(v => {
                    return v.unit.id === el.id
                })
                if (exist) {
                    exist.count++
                } else {
                    this.select.push({
                        id: null,
                        unit: el,
                        count: 1
                    })
                }
                this.$emit('input', this.select)
                this.selectOption = null
            },
            reset () {
                this.select= []
                this.initialize()
            }
        },
        computed: {
            totalChoosePrice() {
                return this.sum(this.select)
            },
            onlyOneOption () {
                return this.options.length ===1
            }
        },
        props: [
            'options', 'alreadyChoose', 'value', 'reset'
        ],
        data() {
            return {
                selectOption: null,
                'select': [],
                pay: 'prepay'
            }
        },
        methods: {
            sum(array) {
                return array.map((v) => {
                    return v.unit.sell_price * v.count
                }).reduce((a, b) => a + b, 0);
            },
            dropOption(key) {
                this.select.splice(key, 1)
            },
            toggleButton () {
                if($("#toggleBtn").hasClass("on")){
                    $("#toggleBtn").removeClass("on")
                }else{
                    $("#toggleBtn").addClass("on")
                }
            },
            initialize () {
                if(this.onlyOneOption && this.select.length===0) this.selectOption=this.options[0]
            }
        },
        mounted() {
            this.select = this.alreadyChoose
            this.$emit('input', this.select)
            this.initialize()
        }
    }
</script>

<style scoped>

</style>
