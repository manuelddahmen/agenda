package com.sample;


public class TableActivites {

  private long idActivite;
  private String nom;
  private long tacheCalendrier;
  private long noAnimateur;


  public long getIdActivite() {
    return idActivite;
  }

  public void setIdActivite(long idActivite) {
    this.idActivite = idActivite;
  }


  public String getNom() {
    return nom;
  }

  public void setNom(String nom) {
    this.nom = nom;
  }


  public long getTacheCalendrier() {
    return tacheCalendrier;
  }

  public void setTacheCalendrier(long tacheCalendrier) {
    this.tacheCalendrier = tacheCalendrier;
  }


  public long getNoAnimateur() {
    return noAnimateur;
  }

  public void setNoAnimateur(long noAnimateur) {
    this.noAnimateur = noAnimateur;
  }

}
