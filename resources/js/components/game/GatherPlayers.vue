<template>
    <div>
        <b-row class="p-5">
            <b-col md="4" lg="4" sm="4">
                <b-row>
                    <b-col md="12">
                        <div class="text-left pt-3">
                            <h3 class="orange-text">Join game</h3>
                        </div>
                        <div class="text-left pt-3">
                            <h4 for="username" class="orange-text">Username</h4>
                            <b-form-input id="username" v-model="newPlayer.name"></b-form-input>
                        </div>
                        <div class="pt-3">
                            <b-button block variant="sld-orange" @click="joinGame">Play!</b-button>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
            <b-col></b-col>
            <b-col md="6" lg="6" sm="6">
                <div class="text-center">
                    <h2 class="orange-text">Game info</h2>
                </div>
                <div class="text-center py-2">
                    <h2 class="orange-text">Round time</h2>
                    <h4 class="orange-text">{{this.game[0].round_time}} seconden</h4>
                </div>
                <div class="text-center py-2">
                    <h2 class="orange-text">Number of rounds</h2>
                    <h4 class="orange-text">{{this.game[0].rounds}}</h4>
                </div>
                <div class="text-center py-2">
                    <h2 class="orange-text">Onderwerpen</h2>
                    <table class="table table-borderless">
                        <tbody class="orange-text">
                            <tr v-for="(subject,index) in this.subjects">
                                <td>{{subject}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center py-2">
                    <h2 class="orange-text">Spelers</h2>
                    <table class="table table-borderless">
                        <tbody class="orange-text">
                            <tr v-for="(player,key) in this.players">
                                <td>{{player.name}}</td>
                            </tr>
                        </tbody>

                    </table>
                </div>
                <div class="text-center" v-if="iAmOwner == true && enoughPlayers == true">
                    <b-button variant="sld-orange" size="lg" block @click="startGame">Start game
                    </b-button>
                </div>
            </b-col>
        </b-row>
        <b-row class="py-5">
            <b-col align-self="center">
                <div class="text-center share-link">
                    <a class="orange-text" :href="lobbyUrl">{{lobbyUrl}}</a>
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
                    name: 'NewPlayerz',
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

<style lang="scss">
    $sld-orange : #e55039;
    .any-list {
        list-style-type: none;
    }

    .orange-text {
        color: $sld-orange;
    }

    .table td{
        padding:0;
    }
    .share-link a:hover{
        color: lighten($sld-orange,10%);
    }
</style>
