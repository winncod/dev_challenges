import Vue from 'vue'
import store from './../store';
const baseEndpoint = 'http://localhost:8081'


export function messageError(content,header){
    Vue.notify({
        group:'main',
        type:'error',
        title: header ?? 'Error',
        text: content
    })
}

export function messageSuccess(content,header){
    Vue.notify({
        group:'main',
        type:'success',
        title: header ?? 'Action completed',
        text: content
    })
}

export async function request(endpoint,method,data,auth ){
    store.commit('showLoading')
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        } 
    }
     
    if (auth) {
        options.headers.Authorization = `Bearer ${getAuth().token}`
    }
    if(data) {
        options.body = JSON.stringify(data)
    }
    const response = await fetch(`${baseEndpoint}/${endpoint}`,options);
    store.commit('hideLoading')
    
    if (response.ok) {
        const content = await response.json()
        return content
    }
    
    const errorResponse =  await response.json()
    if (errorResponse && errorResponse.error) {
        messageError(errorResponse.error,'Action Error')    
    }
    else{
        messageError(response.statusText,'Action Error')
    }
    
    return false
}

export async function joinIssue(user,issue){
    const response = await request(`issue/${issue}/join`,'POST',{name:user},false)
    if(response)
    {
        setAuth({
            name:user,
            token:response.token
        })
        return true;
    }
    return false;
}

export async function getIssue(issue){
    const response = await request(`issue/${issue}`,'GET',null,false)
    return response;
}

export async function voteIssue(issue,value){
    const response = await request(`issue/${issue}/vote`,'POST',{value:value},true)
    return response
}

export function getAuth(){
    return JSON.parse(localStorage.getItem("auth"))
}

export function setAuth(user){
    localStorage.setItem("auth", JSON.stringify(user));
}