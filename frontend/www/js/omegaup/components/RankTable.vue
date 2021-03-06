<template>
  <div class="panel panel-default">
    <div class="panel-heading">
      <template v-if="isIndex">
        <h3 class="panel-title">
          {{ UI.formatString(T.rankHeader, { count: length }) }}
        </h3>
      </template>
      <template v-else="">
        <h3 class="panel-title">
          {{
            UI.formatString(T.rankRangeHeader, {
              lowCount: (page - 1) * length + 1,
              highCount: page * length,
            })
          }}
        </h3>
      </template>
    </div>
    <div class="panel-body" v-if="!isIndex">
      <label
        ><omegaup-autocomplete
          class="form-control"
          v-bind:init="el => typeahead.userTypeahead(el)"
          v-model="searchedUsername"
        ></omegaup-autocomplete
      ></label>
      <button class="btn btn-primary" type="button" v-on:click="onSubmit">
        {{ T.searchUser }}
      </button>
      <template v-if="page &gt; 1">
        <a class="prev" v-bind:href="prevPageFilter">{{ T.wordsPrevPage }}</a>
        <span class="delimiter" v-show="shouldShowNextPage">|</span> </template
      ><a
        class="next"
        v-bind:href="nextPageFilter"
        v-show="shouldShowNextPage"
        >{{ T.wordsNextPage }}</a
      >
      <template v-if="Object.keys(availableFilters).length &gt; 0">
        <select class="filter" v-model="filter" v-on:change="onFilterChange">
          <option value="">
            {{ T.wordsSelectFilter }}
          </option>
          <option
            v-bind:value="key"
            v-for="(item, key, index) in availableFilters"
          >
            {{ item }}
          </option>
        </select>
      </template>
      <template v-else-if="!isLogged &amp;&amp; !isIndex">
        <span class="label label-info">{{ T.mustLoginToFilterUsers }}</span>
      </template>
      <template v-else-if="!isIndex">
        <span class="label label-info">{{
          T.mustUpdateBasicInfoToFilterUsers
        }}</span>
      </template>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <th colspan="2">{{ T.wordsUser }}</th>
          <th class="numericColumn">{{ T.rankScore }}</th>
          <th class="numericColumn" v-if="!isIndex">{{ T.rankSolved }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rank in ranking">
          <td>{{ rank.rank }}</td>
          <td>
            <omegaup-countryflag
              v-bind:country="rank.country"
            ></omegaup-countryflag>
          </td>
          <td class="forcebreaks forcebreaks-top-5">
            <omegaup-user-username
              v-bind:classname="rank.classname"
              v-bind:linkify="true"
              v-bind:username="rank.username"
            ></omegaup-user-username
            ><span v-if="rank.name == null || length == 5">&nbsp;</span>
            <span v-else=""
              ><br />
              {{ rank.name }}</span
            >
          </td>
          <td class="numericColumn">{{ rank.score }}</td>
          <td class="numericColumn" v-if="!isIndex">
            {{ rank.problemsSolvedUser }}
          </td>
        </tr>
      </tbody>
    </table>
    <div class="panel-footer">
      <template v-if="isIndex">
        <a href="/rank/">{{ T.rankViewFull }}</a>
      </template>
      <template v-else="">
        <template v-if="page &gt; 1">
          <a class="prev" v-bind:href="prevPageFilter">{{ T.wordsPrevPage }}</a>
          <span class="delimiter" v-show="shouldShowNextPage"
            >|</span
          > </template
        ><a
          class="next"
          v-bind:href="nextPageFilter"
          v-show="shouldShowNextPage"
          >{{ T.wordsNextPage }}</a
        >
      </template>
    </div>
  </div>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'vue-property-decorator';

import { OmegaUp } from '../omegaup';
import T from '../lang';
import * as UI from '../ui';
import * as typeahead from '../typeahead';
import Autocomplete from './Autocomplete.vue';
import CountryFlag from './CountryFlag.vue';
import user_Username from './user/Username.vue';

interface Rank {
  country: string;
  classname?: string;
  username: string;
  name?: string;
  score: number;
  problemsSolvedUser: number;
}

@Component({
  components: {
    'omegaup-autocomplete': Autocomplete,
    'omegaup-countryflag': CountryFlag,
    'omegaup-user-username': user_Username,
  },
})
export default class RankTable extends Vue {
  @Prop() page!: number;
  @Prop() length!: number;
  @Prop() isIndex!: boolean;
  @Prop() isLogged!: boolean;
  @Prop() availableFilters!: { [key: string]: string };
  @Prop() filter!: string;
  @Prop() ranking!: Rank[];
  @Prop() resultTotal!: number;

  T = T;
  UI = UI;
  typeahead = typeahead;
  searchedUsername = '';

  onSubmit(): void {
    window.location.href = `/profile/${encodeURIComponent(
      this.searchedUsername,
    )}`;
  }

  onFilterChange(): void {
    // change url parameters with jquery
    // https://samaxes.com/2011/09/change-url-parameters-with-jquery/
    let queryParameters: { [key: string]: string } = {};
    const re = /([^&=]+)=([^&]*)/g;
    const queryString = location.search.substring(1);
    let m: string[] | null = null;
    while ((m = re.exec(queryString))) {
      queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }
    if (this.filter !== '') {
      queryParameters['filter'] = this.filter;
    } else {
      delete queryParameters['filter'];
    }
    window.location.search = UI.buildURLQuery(queryParameters);
  }

  get nextPageFilter(): string {
    if (this.filter)
      return `/rank?page=${this.page + 1}&filter=${encodeURIComponent(
        this.filter,
      )}`;
    else return `/rank?page=${this.page + 1}`;
  }

  get prevPageFilter(): string {
    if (this.filter)
      return `/rank?page=${this.page - 1}&filter=${encodeURIComponent(
        this.filter,
      )}`;
    else return `/rank?page=${this.page - 1}`;
  }

  get shouldShowNextPage(): boolean {
    return this.length * this.page < this.resultTotal;
  }
}
</script>
