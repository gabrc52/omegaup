<template>
  <div class="wait_for_ajax panel panel-default">
    <div class="panel-heading">
      <ul class="nav nav-tabs">
        <li class="active" v-on:click="selectedTab = 'codersOfTheMonth'">
          <a data-toggle="tab">{{
            category == 'all' ? T.codersOfTheMonth : T.codersOfTheMonthFemale
          }}</a>
        </li>
        <li v-on:click="selectedTab = 'codersOfPreviousMonth'">
          <a data-toggle="tab">{{
            category == 'all'
              ? T.codersOfTheMonthRank
              : T.codersOfTheMonthFemaleRank
          }}</a>
        </li>
        <li v-on:click="selectedTab = 'candidatesToCoderOfTheMonth'">
          <a data-toggle="tab">{{
            category == 'all'
              ? T.codersOfTheMonthListCandidate
              : T.codersOfTheMonthFemaleListCandidate
          }}</a>
        </li>
      </ul>
    </div>
    <div class="panel-body"></div>
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th></th>
          <th>{{ T.codersOfTheMonthCountry }}</th>
          <th>{{ T.codersOfTheMonthUser }}</th>
          <th v-if="selectedTab == 'codersOfTheMonth'">
            {{ T.codersOfTheMonthDate }}
          </th>
          <th
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth'"
          >
            {{ T.profileStatisticsNumberOfSolvedProblems }}
          </th>
          <th
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth'"
          >
            {{ T.rankScore }}
          </th>
          <th
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth' && isMentor"
          >
            {{ T.wordsActions }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="coder in visibleCoders">
          <td><img v-bind:src="coder.gravatar_32" /></td>
          <td>
            <omegaup-countryflag
              v-bind:country="coder.country_id"
            ></omegaup-countryflag>
          </td>
          <td>
            <omegaup-user-username
              v-bind:classname="coder.classname"
              v-bind:linkify="true"
              v-bind:username="coder.username"
            ></omegaup-user-username>
          </td>
          <td v-if="selectedTab == 'codersOfTheMonth'">{{ coder.date }}</td>
          <td
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth'"
          >
            {{ coder.problems_solved }}
          </td>
          <td
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth'"
          >
            {{ coder.score }}
          </td>
          <td
            class="numericColumn"
            v-if="selectedTab == 'candidatesToCoderOfTheMonth' && isMentor"
          >
            <button
              class="btn btn-primary"
              v-if="canChooseCoder &amp;&amp; !coderIsSelected"
              v-on:click="$emit('select-coder', coder.username)"
            >
              {{
                category == 'all'
                  ? T.coderOfTheMonthChooseAsCoder
                  : T.coderOfTheMonthFemaleChooseAsCoder
              }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator';
import { omegaup } from '../../omegaup';
import T from '../../lang';
import user_Username from '../user/Username.vue';
import country_Flag from '../CountryFlag.vue';

@Component({
  components: {
    'omegaup-user-username': user_Username,
    'omegaup-countryflag': country_Flag,
  },
})
export default class CoderOfTheMonthList extends Vue {
  @Prop() codersOfCurrentMonth!: omegaup.CoderOfTheMonth[];
  @Prop() codersOfPreviousMonth!: omegaup.CoderOfTheMonth[];
  @Prop() candidatesToCoderOfTheMonth!: omegaup.CoderOfTheMonth[];
  @Prop() canChooseCoder!: boolean;
  @Prop() coderIsSelected!: boolean;
  @Prop() isMentor!: boolean;
  @Prop() category!: string;

  T = T;
  selectedTab = 'codersOfTheMonth';

  get visibleCoders(): omegaup.CoderOfTheMonth[] {
    switch (this.selectedTab) {
      case 'codersOfTheMonth':
      default:
        return this.codersOfCurrentMonth;
      case 'codersOfPreviousMonth':
        return this.codersOfPreviousMonth;
      case 'candidatesToCoderOfTheMonth':
        return this.candidatesToCoderOfTheMonth;
    }
  }
}
</script>
