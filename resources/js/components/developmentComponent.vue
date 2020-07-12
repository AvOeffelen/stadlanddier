<template>
    <div>
        <b-row>
            <b-col md="4" xl="4" sm="4" cols="4" lg="4"></b-col>
            <b-col md="4" xl="4" sm="4" cols="4" lg="4">
                <div class="timer text-center">
                    <base-timer :roundTime="roundTime" ref="timer"></base-timer>
                </div>
            </b-col>
            <b-col md="4" xl="4" sm="4" cols="4" lg="4"></b-col>
        </b-row>
        <b-row>
            <b-col md="12" xl="12" sm="12" cols="12" lg="12">
                <div class="text-center">
                    <p class="orange-letter-text">Letter : <span class="orange-letter-text-letter">{{this.currentLetter}}</span></p>
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col></b-col>
            <b-col cols="12" sm="10" md="10" xl="10" class="text-center">
                <b-row v-for="(subject,key) in this.subjects" :key="key">
                    <label class="orange-text orange-label">{{subject}}</label>
                    <b-form-input type="text" v-model="answer"></b-form-input>
                </b-row>
            </b-col>
            <b-col></b-col>
        </b-row>
        <b-row class="py-5">
            <b-col>
                <b-button @click="timerStart">start</b-button>
            </b-col>
        </b-row>
        <b-row class="py-5">
            <b-col>
                <b-button @click="timerStart">start</b-button>
            </b-col>
        </b-row>
        <b-row class="py-5">
            <b-col>
                <b-button @click="timerStart">start</b-button>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "developmentComponent",
        data(){
          return {
              game: null,
              roundResults:null,
              fetchingAnswers:true,
              subjects:[
                  'stad',
                  'land',
                  'dier'
              ],
              currentLetter:'c',
              answer:'',
              roundTime: 5
          };
        },
        mounted(){
            this.$root.$on('timesUp',()=>{
                this.roundEnd();
            });
        },
        created() {
            setTimeout(()=>{
                this.loadRound();
            },500);
            setTimeout(()=>{
                this.game = false;
                this.countDownTimer();
            },2000);

        },
        methods:{
            roundEnd(){
                console.log("tijds op");
            },
            timerStart(){
                // this.$refs.timer.startTimer();
                console.log("clicked");
            },
            countDownTimer() {
                if(this.countDown > 0) {
                    setTimeout(() => {
                        this.countDown -= 1
                        this.countDownTimer()
                    }, 1000)
                }
            },
            loadGame(){

            },
            loadRound(){
                let url = `/game/6cEp20eBmxzevYGncDS24kPb9l0XgNqtIMGTNEK6mwIdW/round/1/answers/get`
                axios.get(url).then(response => {
                    this.roundResults = response.data;
                });
            },
        }
    }
</script>

<style lang="scss">

</style>
