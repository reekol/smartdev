<template>
	<div id="content" class="app-smartdev">
		<AppNavigation>
			<ul>
				<AppNavigationItem v-for="smartd in smartdev"
					:key="smartd.id"
					:title="smartd.name"
					:class="{active: currentSmartdevId === smartd.id}"
					@click="openSmartdev(smartd)">
					<template slot="actions">
						<ActionButton v-if="smartd.data.state === false"
							icon="icon-play"
							@click="playSmartdev(smartd)">
							{{ t('smartd', 'Play smartdev') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-pause"
							@click="pauseSmartdev(smartd)">
							{{ t('smartd', 'Pause smartdev') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
			<AppNavigationSettings>
				<input
					v-model="smartuser.email"
					placeholder="Account email"
					autocomplete="off"
					type="text"
					:disabled="updating">
				<br>
				<input
					v-model="smartuser.password"
					placeholder="Account password"
					autocomplete="new-password"
					type="password"
					:disabled="updating">
				<br>
				<input
					v-model="smartuser.country"
					placeholder="Country phone code."
					autocomplete="off"
					type="text"
					:disabled="updating">
				<br>
				<input
					v-model="smartuser.zone"
					placeholder="Zone ( us | eu )"
					autocomplete="off"
					type="text"
					:disabled="updating">
				<br>
				<input
					v-model="smartuser.type"
					placeholder="Type ( tuya | smart_life )"
					autocomplete="off"
					type="text"
					:disabled="updating">
				<br>
				<input
					type="button"
					class="img_descr"
					value="Save smart credentials"
					@click="saveSmartuser(smartuser)">
			</AppNavigationSettings>
		</AppNavigation>
		<AppContent>
			<div v-if="currentSmartdev">
				<input ref="title"
					v-model="currentSmartdev.name"
					type="text"
					:disabled="updating">
				<br>
				<center>
					<img :src="currentSmartdev.icon" class="img_descr">
				</center>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('smartdev', 'No selected smart device to show.') }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import AppNavigationSettings from '@nextcloud/vue/dist/Components/AppNavigationSettings'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		AppNavigationItem,
		AppNavigationSettings,
	},
	data() {
		return {
			smartdev: [],
			currentSmartdevId: null,
			smartuser: {},
			updating: false,
			loading: true,
		}
	},
	computed: {
		/**
		 * Return the currently selected smartdev object
		 * @returns {Object|null}
		 */
		currentSmartdev() {
			if (this.currentSmartdevId === null) {
				return null
			}
			return this.smartdev.find((smartdev) => smartdev.id === this.currentSmartdevId)
		},

	},
	/**
	 * Fetch list of smartdev when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/smartdev/smartdev'))
			this.smartdev = response.data
			const resuser = await axios.get(generateUrl('/apps/smartdev/user'))
			this.smartuser = resuser.data
		} catch (e) {
			console.error(e)
			showError(t('smartdev', 'Could not fetch smartdev'))
		}
		this.loading = false
	},

	methods: {

		openSmartdev(smartdev) {
			if (this.updating) {
				return
			}
			this.currentSmartdevId = smartdev.id
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},

		async saveSmartuser(smartuser) {
			try {
				const response = await axios.put(generateUrl('/apps/smartdev/user'), smartuser)
				this.smartuser = response.data
			} catch (e) {
				console.error(e)
				showError(t('smartuser', 'Unable to save credentials'))
			}
		},

		async playSmartdev(smartdev) {
			this.updating = true
			try {
				smartdev.data.state = true
				await axios.put(generateUrl(`/apps/smartdev/smartdev/${smartdev.id}`), smartdev)
			} catch (e) {
				console.error(e)
				showError(t('smartdev', 'Could not update the smartdev'))
			}
			this.updating = false
		},

		async pauseSmartdev(smartdev) {
			this.updating = true
			try {
				smartdev.data.state = false
				await axios.put(generateUrl(`/apps/smartdev/smartdev/${smartdev.id}`), smartdev)
			} catch (e) {
				console.error(e)
				showError(t('smartdev', 'Could not update the smartdev'))
			}
			this.updating = false
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	.img_descr {
		border:1px solid #cdcdcd;
		border-radius:5px;
		margin:3px;
		clear:both;
		width:200px;
		float:right
	}

	input[type='text'], input[type='password'] {
		width: 200px !important;
		float:right;
		text-align:right;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>
