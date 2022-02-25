package com.sample;


public class TableEvent {

  private long id;
  private String nom;
  private java.sql.Timestamp dateStart;
  private java.sql.Timestamp dateEnd;
  private java.sql.Timestamp hourStart;
  private java.sql.Timestamp hourEnd;
  private String reccurence;


  public long getId() {
    return id;
  }

  public void setId(long id) {
    this.id = id;
  }


  public String getNom() {
    return nom;
  }

  public void setNom(String nom) {
    this.nom = nom;
  }


  public java.sql.Timestamp getDateStart() {
    return dateStart;
  }

  public void setDateStart(java.sql.Timestamp dateStart) {
    this.dateStart = dateStart;
  }


  public java.sql.Timestamp getDateEnd() {
    return dateEnd;
  }

  public void setDateEnd(java.sql.Timestamp dateEnd) {
    this.dateEnd = dateEnd;
  }


  public java.sql.Timestamp getHourStart() {
    return hourStart;
  }

  public void setHourStart(java.sql.Timestamp hourStart) {
    this.hourStart = hourStart;
  }


  public java.sql.Timestamp getHourEnd() {
    return hourEnd;
  }

  public void setHourEnd(java.sql.Timestamp hourEnd) {
    this.hourEnd = hourEnd;
  }


  public String getReccurence() {
    return reccurence;
  }

  public void setReccurence(String reccurence) {
    this.reccurence = reccurence;
  }

}
