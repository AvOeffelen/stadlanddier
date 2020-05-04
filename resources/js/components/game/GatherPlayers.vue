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
                                <h4>Selected options</h4>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <ul>
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
                                <ul>
                                    <li v-for="(player,key) in this.players">
                                        {{player.name}}
                                    </li>
                                </ul>
                            </b-col>
                        </b-row>
                        <b-row>
                            <b-col>
                                <b-button variant="primary" size="lg" block @click="checkWhichPlayerIAm">TestButton</b-button>
                                <b-button variant="notify-blue" size="lg" block v-if="currentPlayer != null && currentPlayer.is_owner == 1" @click="startGame">Start game</b-button>
                                <b-button variant="notify-blue" size="lg" block v-else disabled class="disabled">Start game</b-button>

                            </b-col>
                        </b-row>
                    </b-card-text>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "GatherPlayers",
        props:[
            'uniquecode',
            'game'
        ],
        data() {
            return {
                newPlayer:{
                    name:'',
                    is_owner:0,
                    location:'192.0.0.1',
                    ready:0
                },
                currentPlayer: null,
                players:null,
                subjects:null,
                loadedGame:null,
                polling:null,
                iAmOwner:false,
                enoughPlayers:false,
            };
        },
        created() {
            this.checkWhichPlayerIAm();
            this.playersToJson();
            this.selectedSubjectsToJson();
            this.pollUpdatePlayers();

        },
        methods: {
            joinGame(){
                axios.post(`/game/${this.uniquecode}/player-join-game`,this.newPlayer)
                .then(response => {
                    this.players.push(response.data);
                    console.log("newPlayer",this.players);

                    this.newPlayer.name = '';
                    this.newPlayer.is_owner = 0;
                    this.newPlayer.location = '192.0.0.1';
                });
            },
            checkWhichPlayerIAm(){
                axios.get(`/game/${this.uniquecode}/player-check`)
                    .then(response => {
                        this.currentPlayer = response.data;
                    });
            },
            playersToJson(){
                this.players = JSON.parse(this.game[0].players);
            },
            selectedSubjectsToJson(){
                this.subjects = JSON.parse(this.game[0].selected_options);
            },
            updatePlayers(){
                axios.get(`/game/${this.uniquecode}/load`)
                    .then(response => {
                        this.loadedGame = response.data;
                        this.players = JSON.parse(this.loadedGame[0].players);
                        if(this.players.length > 2){
                            this.enoughPlayers = !this.enoughPlayers;
                        }
                    });
            },
            pollUpdatePlayers(){
                this.polling = setInterval(() =>{
                    this.updatePlayers();
                },5000);
            },
            startGame(){

            }
        }
    }
</script>

<style scoped>

</style>
