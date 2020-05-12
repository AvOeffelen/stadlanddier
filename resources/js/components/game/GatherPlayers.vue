<template>
    <div>
        <b-row class="p-5">
            <b-col md="4" lg="4" sm="4" align-self="center">
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
                            <div class="text-center"><em>Schrijf je zelf in</em></div>
                        </div>
                    </template>
                    <b-card-text>
                        <div class="text-left pt-3">
                            <label for="username">Username</label>
                            <b-form-input id="username" v-model="newPlayer.name"></b-form-input>
                        </div>
                        <div class="pt-3">
                            <b-button block variant="notify-blue" @click="joinGame">join</b-button>
                        </div>
                    </b-card-text>
                </b-card>
            </b-col>
            <b-col></b-col>
            <b-col md="6" lg="6" sm="6" align-self="center">
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
                            <div class="text-center"><em>Lobby information</em></div>
                        </div>
                    </template>
                    <b-card-text>
                        <b-row>
                            <b-col>
                                <h4>Selected time per round</h4>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <p>{{this.game[0].round_time}} seconden</p>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <h4>Rounds were playing</h4>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <p>{{this.game[0].rounds}}</p>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <h4>Selected options</h4>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <ul class="any-list">
                                    <li v-for="(subject,index) in this.subjects">
                                        {{subject}}
                                    </li>
                                </ul>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <h4>Joined players</h4>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <ul class="any-list">
                                    <li v-for="(player,key) in this.players">
                                        {{player.name}}
                                    </li>
                                </ul>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-button variant="notify-blue" size="lg" block
                                          v-if="iAmOwner == true && enoughPlayers == true" @click="startGame">Start game
                                </b-button>
                                <b-button variant="notify-blue" size="lg" block v-else disabled class="disabled">Start
                                    game
                                </b-button>

                            </b-col>
                        </b-row>
                    </b-card-text>
                </b-card>
            </b-col>
        </b-row>
        <b-row>
            <b-col align-self="center">
                <div class="text-center">
                    <a :href="{lobbyUrl}">{{lobbyUrl}}</a>
                </div>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "GatherPlayers",
        props: [
            'uniquecode',
            'game',
            'cookie'
        ],
        data() {
            return {
                newPlayer: {
                    name: '',
                    is_owner: 0,
                    location: '',
                    ready: 0,
                    connected:0
                },
                currentPlayer: null,
                players: null,
                subjects: null,
                loadedGame: null,
                pollingPlayers: null,
                iAmOwner: false,
                enoughPlayers: false,
                started: false,
                pollingStart: null,
                lobbyUrl: '',
            };
        },
        created() {
            this.selectedSubjectsToJson();
            this.pollUpdatePlayers();
            this.playersToJson();
            this.checkWhichPlayerIAm();
            this.generateLobbyUrl();
            setTimeout(() =>{
                this.pollingGameStart();
            },10000);
        },
        methods: {
            //TODO:: Build check if joined you cant join no-more.
            //TODO:: Make username validation. No empty string allowed.
            joinGame() {
                axios.post(`/game/${this.uniquecode}/player-join-game`, this.newPlayer)
                    .then(response => {
                        this.players.push(response.data);
                        this.currentPlayer = response.data;
                    });
            },
            checkWhichPlayerIAm() {
                for (let p in this.players) {
                    if (this.players[p].location == this.cookie) {
                        this.currentPlayer = this.players[p];
                    }
                }
                this.checkOwnerShip();
            },
            playersToJson() {
                this.players = JSON.parse(this.game[0].players);
            },
            selectedSubjectsToJson() {
                this.subjects = JSON.parse(this.game[0].selected_options);
            },
            updatePlayers() {
                axios.get(`/game/${this.uniquecode}/load`)
                    .then(response => {
                        this.loadedGame = response.data;
                        this.players = JSON.parse(this.loadedGame[0].players);
                    });
            },
            pollUpdatePlayers() {
                this.pollingPlayers = setInterval(() => {
                    this.updatePlayers();
                    if (this.enoughPlayers != true) {
                        this.checkPlayerCount();
                    }
                }, 5000);
            },
            checkOwnerShip() {
                if (this.currentPlayer != null) {
                    if (this.currentPlayer.is_owner == 1) {
                        this.iAmOwner = true;
                    }
                } else {
                }
            },
            startGame() {
                let url = `/game/${this.uniquecode}/start`;
                axios.get(url)
                    .then(response => {
                        window.location = url;
                    });
            },
            pollingGameStart() {
                this.pollingStart = setInterval(() => {
                    if (this.loadedGame[0].started == true) {
                        let url = `/game/${this.uniquecode}/start`;
                        axios.get(url)
                            .then(response => {
                                window.location = url;
                            });
                    }
                }, 5000);
            },
            checkPlayerCount() {
                if (this.players.length >= 2) {
                    this.enoughPlayers = true;
                }
            },
            generateLobbyUrl() {
                this.lobbyUrl = `game/${this.uniquecode}/lobby`;
            },
        }
    }
</script>

<style scoped>
    .any-list {
        list-style-type: none;
    }
</style>
