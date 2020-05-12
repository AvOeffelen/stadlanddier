<template>
    <div>
        <b-row>
            <b-col md="3" lg="3" sm="3">
                <b-card
                    header-tag="header"
                    header-class=""
                    header-bg-variant="notify-blue"
                    header-text-variant="white"
                    footer-tag="footer"
                    class="mb-2 text-center"
                >
                    <template v-slot:header>
                        <div class="header-card">
                            <div class="text-center"><em>players</em></div>
                        </div>
                    </template>
                    <b-card-text>
                        <div v-if="this.game == null">
                            <p>Loading players...</p>
                            <b-spinner small></b-spinner>
                        </div>
                        <table class="table table-borderless" v-else>
                            <tbody>
                            <tr v-for="(player) in this.players">
                                <td>{{player.name}}</td>
                                <td v-if="player.connected == 0">
                                    <b-spinner small></b-spinner>
                                </td>
                                <td v-else><i class="fa fa-check"></i></td>
                            </tr>
                            </tbody>
                        </table>
                    </b-card-text>
                </b-card>
            </b-col>
            <b-col v-if="this.allPlayersConnected == false">
                <div class="text-center">
                    <b-spinner></b-spinner>
                    <p>Please wait untill every player has connected.</p>
                </div>
            </b-col>
            <b-col v-if="this.allPlayersConnected == true && this.gameInProgress == false">
                <p>All players have connected.</p>
                <b-button @click="startGame" v-if="this.canStartGame == true">Start game</b-button>
            </b-col>
            <b-col
                v-if="this.gameInProgress == true && this.allPlayersReady == true && this.roundInProgress == false && this.askNewRound == true">
                <p>Game has started! please wait for the host to start the round.</p>
                <b-button v-if="this.canStartRound == true" @click="setRoundInProgress">Start Round</b-button>
            </b-col>
            <b-col v-if="this.gameInProgress == true && this.allPlayersReady == true && this.roundInProgress == true">
                <b-row v-if="this.currentLetter != '' && this.answerArray != null && this.playingRound == true">
                    <b-col>
                        <p>The letter for this round is: {{this.currentLetter}}</p>
                        <b-row v-for="(subject,key) in this.subjects" :key="key">
                            <label>{{subject}}</label>
                            <b-form-input type="text" v-model="answerArray[subject]"></b-form-input>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col v-else-if="this.showingResults == true && this.askNewRound == true">}
                <b-row>
                    <b-col>
                        {{this.roundResults[0]}}
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
                pollRoundInProgress: false,
                playingRound: false,
                pollGameState: false,
                canStartRound: false,
                canStartGame: false,
                subjects: null,
                roundResults: null,
                showingResults:false,
                askNewRound: true,
            };
        },
        created() {
            setInterval(() => {
                this.loadGame();
                console.log("loading game");
            }, 5000);

            setTimeout(() => {
                this.checkConnectedPlayers();
            }, 6000);

        },
        methods: {
            //NOTEFORSELF::
            createAnswerArray(letter, game) {
                console.log("creating answer array!")
                this.answerArrayCreated = true;
                let url = `/game/${this.uniquecode}/letter/${letter}/make-array`;
                axios.get(url)
                    .then(response => {
                        this.answerArray = response.data;
                        this.startRound(game);
                    });
            },
            checkLetterAccordingToRound(game) {
                this.gameRound = game.current_round;
                let letters = JSON.parse(game.selected_letters);
                for (let l in letters) {
                    if (this.gameRound == l) {
                        this.currentLetter = letters[l];
                    }
                }
            },
            prepRound(game) {
                if (this.answerArray == null && this.roundInProgress == false) {
                    this.checkLetterAccordingToRound(game);
                    this.createAnswerArray(this.currentLetter, game);
                    // test whether this works now.
                    this.roundInProgress = true;
                    this.askNewRound = false;
                }
            },
            showResults() {
                this.showingResults = true;
                this.currentLetter = '';
                this.roundResults = [];
                this.roundResults.push(this.answerArray);
                this.askNewRound = true;

            },
            endRound(game) {
                this.roundInProgress = false;
                this.playingRound = false;
                game.round_in_progress = 0;
                if (this.currentPlayer.is_owner == 1) {
                    this.setRoundNotInProgress();
                }else {
                    console.log("loading game..");
                    this.loadGame();
                    console.log("checking game..",this.game);
                }
                // this.setPlayerNotReady();
                this.showResults();
            },
            startRound(game) {
                this.playingRound = true;
                setTimeout(() => {
                    this.endRound(game);
                }, game.round_time * 1000);
            },
            setRoundNotInProgress() {
                console.log("setting round not in Progress!")
                let url = `/game/${this.uniquecode}/update-end-round`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.loadGame();
                    });
            },
            setRoundInProgress() {
                console.log("Setting round in progress");
                let url = `/game/${this.uniquecode}/set-round-progress`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.checkGameState(this.game);
                    });
            },
            startGame() {
                console.log("starting gAme");
                let url = `/game/${this.uniquecode}/set-progress`;
                axios.put(url)
                    .then(response => {
                        this.game = response.data;
                        this.checkGameState(this.game);
                    });
            },
            checkGameState(game) {
                this.checkPlayersReady();
                if (game.in_progress == 0 && this.allPlayersConnected == true) {
                    this.eligibleToStartGame(game);
                } else if (this.gameInProgress == true && game.round_in_progress == 0 && this.allPlayersReady == true && this.askNewRound == true) {
                    clearInterval(this.pollReadyPlayers);
                    this.eligibleToStartRound(game);
                }
                if (game.in_progress == 1 && this.allPlayersConnected == true) {
                    this.gameInProgress = true;
                }
                
                if (game.in_progress == 1 && game.round_in_progress == 1 && this.currentLetter == '' && this.showingResults == false) {
                    this.prepRound(game);
                    //Todo:: create function which starts once and then timeOut after round end.
                } else if (game.in_progress == 1 && game.round_in_progress == 1 && this.currentletter != '') {
                    console.log("round in progress we dont have to do anything at this point.")
                }
            },
            eligibleToStartRound(game) {
                if (this.currentPlayer.is_owner == 1) {
                    console.log("you are the host.")
                    if (game.in_progress == 1 && this.allPlayersConnected == true && this.allPlayersReady == true && game.round_in_progress == 0) {
                        console.log("youre eligible to start the round");
                        this.canStartRound = true;
                    }
                } else {
                    console.log("you're not eligible to start the round");
                }
            },
            eligibleToStartGame(game) {
                if (this.currentPlayer.is_owner == 1) {
                    if (game.in_progress == 0 && this.allPlayersConnected == true) {
                        this.canStartGame = true;
                    }
                } else {
                    console.log("you're not eligible to start the game");
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
                if (this.allPlayersConnected == false) {
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
                }
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
                                console.log("found player update this player.");
                                this.players[p] == this.currentPlayer;
                            }
                        }
                        this.checkPlayersReady();
                    });
            },
        }
    }
</script>

<style scoped>

</style>
