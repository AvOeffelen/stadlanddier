<template>
    <div>
        <b-row>
            <b-col md="3" lg="3" sm="3" class="pl-5">
                <div class="text-left pt-3 pl-5">
                    <h3 class="orange-text">Players</h3>
                </div>
                <div class="text-left pt-3 pl-5">
                    <div v-if="this.game == null">
                        <b-spinner style="width: 3rem; height:3rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                        <p class="orange-text">Spelers laden...</p>
                    </div>
                    <table class="table table-borderless" v-else>
                        <tbody>
                        <tr v-for="(player) in this.players">
                            <td v-if="player.connected == 0">
                                <b-spinner style="width: 1rem; height:1rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                            </td>
                            <td v-else><i class="orange-text fa fa-check"></i></td>
                            <td class="orange-text">{{player.name}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </b-col>
            <b-col v-if="this.allPlayersConnected == false">
                <b-row class="text-center">
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <b-spinner style="width: 8rem; height:8rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                    </b-col>
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <h3 class="orange-text">Wachten tot elke speler verbonden is...</h3>
                    </b-col>
                </b-row>
            </b-col>
            <b-col v-if="this.allPlayersConnected == true && this.gameInProgress == false && this.gameFinished == false">
                <b-row class="text-center">
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <b-spinner style="width: 8rem; height:8rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                    </b-col>
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <h3 class="orange-text">Alle spelers zijn verbonden...</h3>
                    </b-col>
                    <b-col>
                        <b-button size="lg" variant="sld-orange" @click="startGame" v-if="this.canStartGame == true">Start game</b-button>
                    </b-col>
                </b-row>
            </b-col>
            <b-col
                v-if="this.gameInProgress == true
                && this.allPlayersReady == true
                && this.roundInProgress == false
                && this.askNewRound == true
                && this.showingResults == false
                && this.gameFinished == false
                ">
                <b-row>
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <div class="text-center">
                            <b-spinner style="width: 8rem; height:8rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                        </div>
                    </b-col>
                    <b-col md="12" xl="12" sm="12" align-self="center">
                        <div class="text-center">
                            <h3 class="orange-text">Spel is gestart, de ronde word voorbereid...</h3>
                            <b-button size="lg" variant="sld-orange" v-if="this.canStartRound == true && allPlayersReady == true && showingResults == false"
                                      @click="setRoundInProgress">Start Round
                            </b-button>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
            <b-col v-if="this.gameInProgress == true && this.allPlayersReady == true && this.roundInProgress == true && this.gameFinished == false">
                <div v-if="this.currentLetter != '' && this.answerArray != null && this.playingRound == true">
                    <b-row>
                        <b-col md="4" xl="4" sm="4" cols="4" lg="4"></b-col>
                        <b-col md="4" xl="4" sm="4" cols="4" lg="4">
                            <div class="timer text-center">
                                <base-timer :roundTime="roundTime" ref="baseTimer" />
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
                                <label class="orange-text">{{subject}}</label>
                                <b-form-input type="text" v-model="answerArray[subject]['answer']"></b-form-input>
                            </b-row>
                        </b-col>
                        <b-col></b-col>
                    </b-row>
                </div>
            </b-col>
<!--            TODO:: check if this showinSCores made this work.-->
            <b-col v-else-if="this.showingResults == true">
                <div v-if="fetchingAnswers == true">
                    <b-row class="text-center">
                        <b-col md="12" xl="12" sm="12" align-self="center">
                            <b-spinner style="width: 8rem; height:8rem;" label="Large Spinner" type="grow" class="orange-text"></b-spinner>
                        </b-col>
                        <b-col md="12" xl="12" sm="12" align-self="center">
                            <h3 class="orange-text">Even geduld... We zijn alle antwoorden aan het ophalen..</h3>
                        </b-col>
                    </b-row>
                </div>
                <div v-else>
                    <b-row>
                        <b-col md="12" xl="12">
                            <div class="text-center">
                                <p class="orange-letter-text">Letter : <span class="orange-letter-text-letter">{{this.roundResults[0].letter}}</span></p>
                            </div>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col md="12" xl="12">
                            <b-tabs fill class="tabs">
                                <b-tab v-for="(singleResult,key) in this.roundResults" :key="key" class="test-tab" :title="singleResult.player_name">
                                    <b-row class="py-2">
                                        <b-col md="3" xl="3"></b-col>
                                        <b-col md="6" xl="6">
                                            <div class="justify-content-center">
                                                <table class="table table-borderless">
                                                    <tbody>
                                                    <tr v-for="(answer,key) in JSON.parse(singleResult.answer)" :key="key">
                                                        <td v-if="key !== 'letter'"><span>{{key}}</span></td>
                                                        <td v-if="key !== 'letter'"><span><i>{{answer.answer}}</i></span></td>
                                                        <td v-if="key !== 'letter'"><span>{{answer.points}}</span></td>
                                                    </tr>
                                                    <tr class="border-top-orange">
                                                        <td v-if="key !== 'letter'"><span>Totaal</span></td>
                                                        <td v-if="key !== 'letter'"><span><i>&nbsp;</i></span></td>
                                                        <td v-if="key !== 'letter'"><span>{{singleResult.points}}</span></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </b-col>
                                        <b-col md="3" xl="3"></b-col>
                                    </b-row>
                                </b-tab>
                            </b-tabs>
                        </b-col>
                    </b-row>
                    <b-row class="py-2">
                        <b-col></b-col>
                        <b-col md="8" xl="8" v-if="this.canStartRound == true && allPlayersReady == true && disabledButton == false && showingScores == false && this.gameFinished == false">
                            <b-button block variant="sld-orange"
                                      @click="setRoundInProgress">Start Round
                            </b-button>
                        </b-col>
                        <b-col md="8" xl="8" v-if="this.gameFinished == true && showingScores == false && showingResults == true">
                            <h4 class="orange-text">Het spel is klaar, klik door om de scores te bekijken!</h4>
                            <b-button block variant="sld-orange"
                                      @click="showScores">Show scores
                            </b-button>
                        </b-col>
                        <b-col></b-col>
                    </b-row>
                </div>
            </b-col>
            <b-col v-else-if="showingResults == false && gameFinished == true && showingScores == true">
                <b-row>
                    <b-col>
                        <p>The game is finished, we're getting the results for you!</p>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "Game",
        props: [
            'uniquecode',
        ],
        data() {
            return {
                game: null,
                allPlayersConnected: false,
                players: null,
                currentPlayer: null,
                pollConnectedPlayers: false,
                gameInProgress: false,
                pollReadyPlayers: false,
                allPlayersReady: false,
                currentLetter: '',
                roundInProgress: false,
                answerArray: null,
                playingRound: false,
                canStartRound: false,
                canStartGame: false,
                subjects: null,
                roundResults: null,
                showingResults: false,
                askNewRound: true,
                disabledButton: false,
                gameFinished: false,
                currRound:0,
                pollAnswers:false,
                fetchingAnswers:true,
                roundTime:0,
                showingScores:false,
                gotAllAnswers:false,
                AnswersAreCalculated:false,
            };
        },
        created() {
            setInterval(() => {
                this.loadGame();
            }, 5000);

            setTimeout(() => {
                this.checkConnectedPlayers();
                this.countDownTimer();
            }, 6000);

        },
        mounted(){
            this.$root.$on('timesUp',()=>{
                this.TimeIsUp();
            });
        },
        methods: {
            //NOTEFORSELF::
            /**
             * Where the fuck does this JSON error come from?
             */

            showScores(){
                this.showingScores = true;
                this.showingResults = false;
                this.gameInProgress = false;
            },
            countDownTimer() {
                if(this.countDown > 0) {
                    setTimeout(() => {
                        this.countDown -= 1
                        this.countDownTimer()
                    }, 1000)
                }
            },
            createAnswerArray(letter, game) {
                this.answerArrayCreated = true;
                let url = `/game/${this.uniquecode}/letter/${letter}/make-array`;
                axios.get(url)
                    .then(response => {
                        this.answerArray = response.data;
                        this.startRound(game);
                    });
            },
            checkLetterAccordingToRound(game) {
                this.currRound = game.current_round - 1;
                let letters = JSON.parse(game.selected_letters);
                for (let [i, l] in letters) {
                    if (this.currRound == i) {
                        this.currentLetter = letters[i];
                    }
                }
            },
            prepRound(game) {
                if (this.answerArray == null && this.roundInProgress == false) {
                    this.checkLetterAccordingToRound(game);
                    this.createAnswerArray(this.currentLetter, game);
                    this.roundInProgress = true;
                    this.askNewRound = false;
                }
            },
            startRound(game) {
                this.AnswersAreCalculated = false;
                clearInterval(this.pollAnswers);
                this.disabledButton = true;
                this.setPlayerNotReady();
                this.playingRound = true;
                this.canStartRound = false;
                this.askNewRound = false;
                setTimeout(()=>{
                    this.$refs.baseTimer.startTimer();
                },100);
            },
            TimeIsUp(){
                this.roundInProgress = false;
                this.playingRound = false;
                this.endRound(this.game[0]);
            },
            endRound(game) {
                console.log("ending round");
                game.round_in_progress = 0;
                if (this.currentPlayer.is_owner == 1) {
                    this.setRoundNotInProgress();
                    setTimeout(() => {
                        this.disabledButton = false;
                    }, 5000);
                }
                this.storeAnswer();
                this.showResults(game);
            },
            showResults(game) {
                this.showingResults = true;
                this.setRoundResults();
                this.pollAnswers = setInterval(() => {
                    this.getAnswers(this.game[0]);
                    this.checkIfWeGotAllAnswers(this.roundResults,this.game[0]);
                },2000);
                this.askNewRound = true;
                this.resetAnswerArrayAndCurrentLetter();
                this.roundInProgress = false;
                this.setPlayerReady();
            },
            getAnswers(game){
                let url = `/game/${this.uniquecode}/round/${game.current_round}/answers/get`
                axios.get(url).then(response => {
                    this.roundResults = response.data;
                });
            },
            //This function needs re-doing. We need to wait untill the answers are compared and we have greenlight for us to show them.
            //Json error might occur here.
            checkIfWeGotAllAnswers(answers){
                if(answers.length == this.players.length){
                    clearInterval(this.pollAnswers);
                    this.fetchingAnswers = false;
                    //this might the issue
                    if (this.currentPlayer.is_owner == 1) {
                        this.compareAnswers(this.game[0]);
                    } else {

                    }
                } else {

                }
            },
            setRoundResults(){
                this.roundResults = [];
                this.roundResults.push(this.answerArray);
            },
            setRoundNotInProgress() {
                let url = `/game/${this.uniquecode}/update-end-round`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.loadGame();
                    });
            },
            setRoundInProgress() {
                let url = `/game/${this.uniquecode}/set-round-progress`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.checkGameState(this.game);
                    });
            },
            resetAnswerArrayAndCurrentLetter(){
              this.answerArray = null;
              this.currentLetter = '';
            },
            startGame() {
                let url = `/game/${this.uniquecode}/set-progress`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.checkGameState(this.game);
                    });
            },
            /*
                We should be making a state that where the game waits for the command to show the endgame scores.

             */
            checkGameState(game) {
                this.checkPlayersReady();
                if (this.allPlayersConnected == true) {
                    clearInterval(this.pollConnectedPlayers);
                }
                if (this.allPlayersReady == true) {
                    clearInterval(this.pollReadyPlayers);
                }
                if (game.in_progress == 0 && this.allPlayersConnected == true) {
                    this.eligibleToStartGame(game);
                } else if (this.gameInProgress == true && game.round_in_progress == 0 && this.allPlayersReady == true && this.askNewRound == true) {
                    this.eligibleToStartRound(game);
                }
                if (game.finished == 0 && game.in_progress == 1 && this.allPlayersConnected == true) {
                    this.gameInProgress = true;
                }
                if (game.finished == 0 && game.in_progress == 1 && game.round_in_progress == 1 && this.currentLetter == '') {
                    this.showingResults = false;
                }
                if (game.finished == 0 && game.in_progress == 1 && game.round_in_progress == 1 && this.currentLetter == '' && this.showingResults == false && this.answerArray == null) {
                    this.prepRound(game);
                } else if (game.finished == 0 && game.in_progress == 1 && game.round_in_progress == 1 && this.currentletter != '') {

                } else if (game.finished == 1) {
                    console.log('games done, should be here..');
                    // this.gameFinished = true;
                }
            },
            eligibleToStartRound(game) {
                if (this.currentPlayer.is_owner == 1) {
                    if (game.in_progress == 1 && this.allPlayersConnected == true && this.allPlayersReady == true && game.round_in_progress == 0) {
                        this.canStartRound = true;
                    }
                } else {

                }
            },
            eligibleToStartGame(game) {
                if (this.currentPlayer.is_owner == 1) {
                    if (game.in_progress == 0 && this.allPlayersConnected == true) {
                        this.canStartGame = true;
                    }
                } else {
                }
            },
            loadGame() {
                let url = `/game/${this.uniquecode}/load`;
                axios.get(url)
                    .then(response => {
                        this.game = response.data;
                        this.players = JSON.parse(this.game[0].players);
                        if (this.currentPlayer == null) {
                            this.getPlayersCookie();
                        }
                        if (this.subjects == null) {
                            this.subjects = JSON.parse(this.game[0].selected_options);
                        }
                        this.checkGameState(this.game[0]);

                        if(this.roundTime == 0){
                            this.setRoundTime(this.game[0]);
                        }
                    });
            },
            checkPlayersReady() {
                this.pollReadyPlayers = setInterval(() => {
                    let playersReady = 0;
                    for (let p in this.players) {
                        if (this.players[p].ready == 1) {
                            playersReady++;
                        }
                    }
                    if (playersReady == this.players.length) {
                        this.allPlayersReady = true;
                    }
                }, 5000);
            },
            checkConnectedPlayers() {
                this.pollConnectedPlayers = setInterval(() => {
                    let playersConnected = 0;
                    for (let p in this.players) {
                        if (this.players[p].connected == 1) {
                            playersConnected++;
                        }
                    }
                    if (playersConnected == this.players.length) {
                        this.allPlayersConnected = true;
                    } else {
                    }
                }, 5000);
            },
            getPlayersCookie() {
                axios.put('/game/checkCookie')
                    .then(response => {
                        this.cookie = response.data;
                        this.checkPlayer();
                    });
            },
            checkPlayer() {
                for (let p in this.players) {
                    if (this.players[p].location == this.cookie) {
                        this.players[p].connected = 1;
                        this.players[p].ready = 1;
                        this.currentPlayer = this.players[p];
                        this.setPlayerConnected();
                    }
                }
            },
            setPlayerConnected() {
                let url = `/game/${this.uniquecode}/set-player-connected`;
                axios.put(url, this.currentPlayer)
                    .then(response => {
                        this.currentPlayer = response.data;
                    });
            },
            setPlayerNotReady() {
                this.currentPlayer.ready = 0;
                let url = `/game/${this.uniquecode}/set-player-not-ready`;
                axios.put(url, this.currentPlayer)
                    .then(response => {
                        this.currentPlayer = response.data;
                        for (let p in this.players) {
                            if (this.players[p].location == this.currentPlayer.location) {
                                this.players[p] == this.currentPlayer;
                            }
                        }
                        this.checkPlayersReady();
                    });
            },
            storeAnswer() {
                let url = `/game/${this.uniquecode}/store-answer`;
                axios.post(url, this.answerArray).then(response => {

                });
            },
            setRoundTime(game){
                this.roundTime = game.round_time;
            },
            compareAnswers(game){
                let url = `/game/${this.uniquecode}/round/${game.current_round}/answers/compare`
                axios.get(url).then(response => {
                    this.roundResults = response.data;
                    this.AnswersAreCalculated = true;
                });
            },
            setPlayerReady() {
                this.currentPlayer.ready = 1;
                let url = `/game/${this.uniquecode}/set-player-ready`;
                axios.put(url, this.currentPlayer)
                    .then(response => {
                        this.currentPlayer = response.data;
                        for (let p in this.players) {
                            if (this.players[p].location == this.currentPlayer.location) {
                                this.players[p] == this.currentPlayer;
                            }
                        }
                        this.checkPlayersReady();
                    });
            },
        }
    }
</script>

<style lang="scss">

    $sld-orange: #e55039;

    .orange-text{
       color: $sld-orange;
    }


    .orange-letter-text {
        color: $sld-orange;
        font-size: 38px;
    }
    .orange-letter-text-letter {
        color: $sld-orange;
        font-size: 42px;
    }
    .orange-label{
        font-size: 22px;
    }

    $sld-orange : #e55039;
    .nav-tabs .nav-link.active {
        background-color: lighten($sld-orange,0%);
        border-color: lighten($sld-orange,0%);
        color: #ffffff;
    }
    .nav-tabs.nav-link.active:hover {
        color: #ffffff;
    }

    .nav-tabs{
        color: lighten($sld-orange,5%);
        /*background-color: lighten(#10366b,5%);*/
        border-bottom: 1px solid darken($sld-orange,0%);
    }
    .nav-tabs a{
        color: darken($sld-orange,0%);
    }
    .nav-tabs a:hover{
        color: lighten($sld-orange,5%);
    }
    .nav-tabs .nav-link:hover{
        color: lighten($sld-orange,5%);
        /*background-color: lighten(#10366b,5%);*/
        border: 1px solid lighten($sld-orange,0%);
    }
    .nav-tabs .nav-link.active:hover{
        color: lighten(#ffffff,5%);
        /*background-color: lighten(#10366b,5%);*/
        border: 1px solid lighten($sld-orange,0%);
    }

    .tabs table span{
        color :$sld-orange;
        font-size: 35px;
    }

    .border-top-orange{
        border-top: 3px solid $sld-orange;
    }
    .border-top-orange span{
        font-size: 38px;
    }
    .orange-letter-text {
        color: $sld-orange;
        font-size: 38px;
    }
    .orange-letter-text-letter {
        color: $sld-orange;
        font-size: 42px;
    }
    .orange-text{
        color: $sld-orange;
    }
    .orange-label{
        font-size: 22px;
    }
    .timer{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
    }
</style>
